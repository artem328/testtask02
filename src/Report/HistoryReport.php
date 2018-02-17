<?php

namespace App\Report;

use App\Entity\Portfolio;
use App\Entity\Transaction;
use App\Finance\Api\ApiClientInterface;
use App\Finance\Api\ApiException;
use App\Report\HistoryReport\Item;
use App\Report\HistoryReport\Iterator;
use App\Stock\Stock;
use App\Stock\StockContainer;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

class HistoryReport
{
    const INTERVAL_DAY = 'day';
    const INTERVAL_MONTH = 'month';
    const INTERVAL_YEAR = 'year';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Entity\Portfolio
     */
    private $portfolio;

    /**
     * @var string
     */
    private $interval;

    /**
     * @var \App\Entity\Transaction[]
     */
    private $transactions;

    /**
     * @var \App\Report\HistoryReport\Item
     */
    private $firstItem;

    /**
     * @var \App\Finance\Api\ApiClientInterface
     */
    private $financeApiClient;

    /**
     * @var array
     */
    private $historicalData = [];

    /**
     * @var string
     */
    private $dateFormat;

    /**.
     * @var array
     */
    private $intervals = [
        self::INTERVAL_DAY => '1D',
        self::INTERVAL_MONTH => '1M',
        self::INTERVAL_YEAR => '1Y',
    ];

    /**
     * @var \DateTime
     */
    private $fromDate;

    /**
     * @var \Symfony\Component\Cache\Simple\FilesystemCache
     */
    private $cache;

    /**
     * SummaryReport constructor.
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Entity\Portfolio $portfolio
     * @param \App\Finance\Api\ApiClientInterface $financeApiClient
     * @param string $interval
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Portfolio $portfolio,
        ApiClientInterface $financeApiClient,
        string $interval = self::INTERVAL_MONTH
    ) {
        $this->entityManager = $entityManager;
        $this->portfolio = $portfolio;
        $this->interval = $interval;
        $this->financeApiClient = $financeApiClient;

        // Hardcode here
        $this->fromDate = new DateTime('-2years');

        $now = new DateTime();
        $tomorrow = new DateTime('tomorrow');
        // Initialize cache storage till next day
        $this->cache = new FilesystemCache('historyReport', $tomorrow->getTimestamp() - $now->getTimestamp());

        $this->initDateFormat();
        $this->initTransactions();
        $this->initHistoricalData();
        $this->initItems();

    }

    /**
     * @return array
     */
    public static function getIntervals(): array
    {
        return [
            static::INTERVAL_DAY,
            static::INTERVAL_MONTH,
            static::INTERVAL_YEAR,
        ];
    }

    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     */
    public function setDateFormat(string $dateFormat): void
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @return string
     */
    public function getInterval(): string
    {
        return $this->interval;
    }

    /**
     * @return \App\Entity\Portfolio
     */
    public function getPortfolio(): Portfolio
    {
        return $this->portfolio;
    }

    /**
     * @return \App\Report\HistoryReport\Item
     */
    public function getFirstItem(): Item
    {
        return $this->firstItem;
    }

    /**
     * @return \App\Report\HistoryReport\Iterator
     */
    public function getIterator(): Iterator
    {
        return new Iterator($this->firstItem);
    }

    private function initDateFormat(): void
    {
        if (static::INTERVAL_YEAR === $this->getInterval() || static::INTERVAL_MONTH === $this->getInterval()) {
            $this->dateFormat = 'F, Y';
        } else {
            $this->dateFormat = 'Y-m-d';
        }
    }

    private function initTransactions(): void
    {
        $this->transactions = $this->entityManager->getRepository(Transaction::class)
            ->findAllTransactionsInPortfolio($this->portfolio);
    }

    private function initHistoricalData(): void
    {
        $allSymbols = $this->entityManager->getRepository(Transaction::class)
            ->findAllSymbolsInPortfolio($this->portfolio);
        $today = new DateTime('today');

        foreach ($allSymbols as $symbol) {
            if ($this->cache->has($symbol)) {
                $this->historicalData[$symbol] = $this->cache->get($symbol);
            } else {
                $this->downloadHistoricalData($symbol, $this->fromDate, $today);
                $this->cache->set($symbol, $this->historicalData[$symbol]);
            }
        }

    }

    private function initItems(): void
    {
        $firstTransaction = reset($this->transactions);

        if (!$firstTransaction) {
            return;
        }

        $repository = $this->entityManager->getRepository(Transaction::class);

        $today = new DateTime('today');
        $dateStart = $firstTransaction->getCreatedAt() > $this->fromDate ?
            $firstTransaction->getCreatedAt() :
            $this->fromDate;

        $stocks = $repository->getStocksInPortfolio($this->portfolio, null, $dateStart);
        $this->fillStocksWithHistoricalData($stocks);
        $this->firstItem = new Item($stocks, $dateStart);

        $item = $this->firstItem;

        $date = clone $dateStart;
        do {
            $nextDate = $this->getNextDate($date);
            $endOfNextDate = clone $nextDate;
            $endOfNextDate->setTime(23, 59, 59);


            // If there were transactions in portfolio between
            // last date and next date, then quantity of stocks might be changed
            // so get new set of stocks and fill them with historical data also
            // If no changes then we just use existed set of stocks
            if ($this->wasTransactionBetween($date, $endOfNextDate)) {
                $stocks = $repository->getStocksInPortfolio($this->portfolio, null, $endOfNextDate);
                $this->fillStocksWithHistoricalData($stocks);
            }

            // Also fill stocks with actual prices
            // for current date item
            if ($today == $nextDate) {
                $this->fillStocksWithQuotes($stocks);
            }

            $nextItem = new Item($stocks, $nextDate);

            $item->setNext($nextItem);
            $nextItem->setPrevious($item);

            $item = $nextItem;
            $date = $nextDate;
        } while ($nextDate < $today);
    }

    /**
     * @param \App\Stock\StockContainer $stocks
     */
    private function fillStocksWithHistoricalData(StockContainer $stocks): void
    {
        $stocks->each(function (Stock $stock) {
            $historicalData = $this->historicalData[$stock->getSymbol()] ?? [];

            foreach ($historicalData as $data) {
                $stock->addHistoricalData($data);
            }
        });
    }

    /**
     * @param \App\Stock\StockContainer $stocks
     */
    private function fillStocksWithQuotes(StockContainer $stocks): void
    {
        $symbols = $stocks->getAllSymbols();
        $quotes = [];
        try {
            $fetchedQuotes = $this->financeApiClient->getQuotes($symbols);
            foreach ($fetchedQuotes as $quote) {
                $quotes[$quote->getSymbol()] = $quote;
            }
        } catch (ApiException $exception) {
        }

        $stocks->each(function (Stock $stock) use ($quotes) {
            if (isset($quotes[$stock->getSymbol()])) {
                $stock->setQuote($quotes[$stock->getSymbol()]);
            }
        });
    }

    /**
     * @param string $symbol
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     */
    private function downloadHistoricalData(string $symbol, DateTime $dateFrom, DateTime $dateTo): void
    {
        if (!isset($this->historicalData[$symbol])) {
            $this->historicalData[$symbol] = [];
        }

        try {
            $this->historicalData[$symbol] = array_merge($this->historicalData[$symbol],
                $this->financeApiClient->getHistoricalData($symbol, $dateFrom, $dateTo));
        } catch (ApiException $exception) {
            // Skip if couldn't fetch data
        }
    }

    /**
     * @param \DateTime $date
     * @return \DateTime
     */
    private function getNextDate(DateTime $date): DateTime
    {
        $nextDate = clone $date;
        $nextDate->add(new DateInterval('P'.$this->intervals[$this->getInterval()]));
        $today = new DateTime('today');

        return $nextDate > $today ? $today : $nextDate;
    }

    /**
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @return bool
     */
    private function wasTransactionBetween(DateTime $dateStart, DateTime $dateEnd): bool
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getCreatedAt() >= $dateStart && $transaction->getCreatedAt() <= $dateEnd) {
                return true;
            }
        }

        return false;
    }
}