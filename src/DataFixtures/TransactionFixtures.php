<?php

namespace App\DataFixtures;

use App\Entity\Portfolio;
use App\Entity\Transaction;
use App\Finance\Api\ApiClientInterface;
use App\Finance\Api\ApiException;
use App\Finance\Api\HistoricalDataInterface;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var \App\Finance\Api\ApiClientInterface
     */
    private $financeApiClient;

    /**
     * @var array
     */
    private $symbols = [
        'AAPL',
        'GOOG',
        'NKE',
        'GDDY',
        'FB',
        'TWTR',
        'YNDX',
    ];

    /**
     * @var array
     */
    private $historicalData = [];

    /**
     * @var int
     */
    private $minDaysBefore = 30;

    /**
     * @var int
     */
    private $maxDaysBefore = 365 * 2;

    /**
     * @var int
     */
    private $minTransactions = 1;

    /**
     * @var int
     */
    private $maxTransactions = 30;

    /**
     * @var int
     */
    private $maxTransactionQuantity = 10;

    /**
     * TransactionFixtures constructor.
     * @param \App\Finance\Api\ApiClientInterface $financeApiClient
     */
    public function __construct(ApiClientInterface $financeApiClient)
    {
        $this->financeApiClient = $financeApiClient;
        $this->initHistoricalData();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies(): array
    {
        return [
            PortfolioFixtures::class,
        ];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $portfolios = $manager->getRepository(Portfolio::class)
            ->findAll();

        foreach ($portfolios as $portfolio) {
            $transactions = rand($this->minTransactions, $this->maxTransactions);
            $date = $this->getDate();

            $portfolioStocks = [];

            for ($i = 0; $i < $transactions; $i++) {
                $transaction = new Transaction();
                $transaction->setPortfolio($portfolio);
                $transaction->setCreatedAt($date);

                $symbol = $this->getSymbol();
                $transaction->setSymbol($symbol);

                if ($h = $this->getHistoricalData($symbol, $transaction->getCreatedAt())) {
                    $avgPrice = ($h->getOpenPrice() + $h->getClosePrice() + $h->getLowPrice() + $h->getHighPrice()) / 4;
                    $transaction->setPrice($avgPrice);
                }

                $date = $this->getDate($date);

                if (!$transaction->getPrice()) {
                    continue;
                }

                if (!isset($portfolioStocks[$transaction->getSymbol()])) {
                    $portfolioStocks[$transaction->getSymbol()] = 0;
                }

                $transaction->setQuantity(rand(1, $this->maxTransactionQuantity));

                $total = $transaction->getPrice() * $transaction->getQuantity();

                // Buy or sell
                // If random number is 1
                // then it's a sell operation
                if ($portfolioStocks[$transaction->getSymbol()] > $transaction->getQuantity() && rand(0, 1)) {
                    $total *= -1;
                }

                $transaction->setTotal($total);

                $portfolioStocks[$transaction->getSymbol()] += $transaction->getQuantity() * ($transaction->getOperation() === 'sell' ? -1 : 1);

                $manager->persist($transaction);
            }
        }
        $manager->flush();
    }

    /**
     * @param \DateTime|null $date
     * @return \DateTime
     */
    private function getDate(?DateTime $date = null): DateTime
    {
        if (null === $date) {
            $date = new DateTime('-'.rand($this->minDaysBefore, $this->maxDaysBefore).'days');
            $date->setTime(0, 0);

            return $date;
        }

        $now = new DateTime();
        $now->setTime(0, 0);

        if ($date >= $now) {
            return clone $now;
        }

        do {
            $diff = $date->diff($now);
            $newDate = clone $date;
            $newDate->add(new DateInterval('P'.rand(0, $diff->days).'D'));
        } while ($newDate > $now);

        return $newDate;
    }

    /**
     * @return string
     */
    private function getSymbol(): string
    {
        return $this->symbols[rand(0, count($this->symbols) - 1)];
    }

    private function initHistoricalData(): void
    {
        $maxDate = new DateTime('-'.$this->maxDaysBefore.'days');
        $now = new DateTime();

        foreach ($this->symbols as $symbol) {
            try {
                $historicalDataItems = $this->financeApiClient->getHistoricalData($symbol, $maxDate, $now);

                foreach ($historicalDataItems as $historicalData) {
                    $this->historicalData[$symbol][$historicalData->getDate()->format('Y-m-d')] = $historicalData;
                }
            } catch (ApiException $exception) {

            }
        }
    }

    /**
     * @param string $symbol
     * @param \DateTime $date
     * @return \App\Finance\Api\HistoricalDataInterface|null
     */
    private function getHistoricalData(string $symbol, DateTime $date): ?HistoricalDataInterface
    {
        if (!isset($this->historicalData[$symbol])) {
            return null;
        }

        $dateIndex = $date->format('Y-m-d');

        if (!isset($this->historicalData[$symbol][$dateIndex])) {
            return null;
        }

        return $this->historicalData[$symbol][$dateIndex];
    }
}