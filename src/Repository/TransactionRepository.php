<?php

namespace App\Repository;

use App\Entity\Portfolio;
use App\Entity\Transaction;
use App\Finance\Api\ApiClientInterface;
use App\Finance\Api\ApiException;
use App\Stock\Stock;
use App\Stock\StockContainer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TransactionRepository extends ServiceEntityRepository
{

    /**
     * @var \App\Finance\Api\ApiClientInterface
     */
    private $financeApiClient;

    /**
     * TransactionRepository constructor.
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry
     * @param \App\Finance\Api\ApiClientInterface $financeApiClient
     */
    public function __construct(RegistryInterface $registry, ApiClientInterface $financeApiClient)
    {
        parent::__construct($registry, Transaction::class);
        $this->financeApiClient = $financeApiClient;
    }

    /**
     * @param \App\Entity\Portfolio $portfolio
     * @param string $symbol
     * @return int
     */
    public function countStocksInPortfolio(Portfolio $portfolio, string $symbol): int
    {
        $results = $this->createQueryBuilder('t')
            ->where('t.symbol = :symbol')->setParameter('symbol', $symbol)
            ->andWhere('t.portfolio = :portfolio')->setParameter('portfolio', $portfolio)
            ->groupBy('t.symbol')
            ->select('SUM(CASE WHEN t.total < 0 THEN -1 * t.quantity ELSE t.quantity END) as owned_stocks')
            ->getQuery()
            ->execute();

        $result = $results[0] ?? [];

        return (int)$result['owned_stocks'] ?? 0;
    }

    /**
     * @param \App\Entity\Portfolio $portfolio
     * @return \App\Stock\StockContainer
     *
     */
    public function getStocksInPortfolio(Portfolio $portfolio): StockContainer
    {
        $rawResults = $this->createQueryBuilder('t')
            ->where('t.portfolio = :portfolio')->setParameter('portfolio', $portfolio)
            ->groupBy('t.symbol')
            ->select('t.symbol')
            ->addSelect('SUM(CASE WHEN t.total < 0 THEN -1 * t.quantity ELSE t.quantity END) as quantity')
            ->addSelect('SUM(t.total) AS total')
            ->getQuery()
            ->execute();

        $results = [];

        $symbols = array_map(function ($item) {
            return $item['symbol'];
        },
            $rawResults);

        $quotes = [];
        try {
            $quotesList = $this->financeApiClient->getQuotes($symbols);
            foreach ($quotesList as $quote) {
                $quotes[$quote->getSymbol()] = $quote;
            }
        } catch (ApiException $exception) {
        }

        foreach ($rawResults as $result) {
            $stock = new Stock();
            $stock->setSymbol($result['symbol']);
            $stock->setPortfolio($portfolio);
            $stock->setTotal($result['total']);

            /** @var \App\Finance\Api\QuoteInterface $quote */
            if ($quote = $quotes[$result['symbol']] ?? null) {
                $stock->setName($quote->getName());
                $stock->setQuote($quote);
            }

            $stock->setQuantity($result['quantity']);
            $results[] = $stock;
        }

        return new StockContainer($results);
    }
}
