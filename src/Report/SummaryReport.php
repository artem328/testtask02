<?php

namespace App\Report;

use App\Entity\Portfolio;
use App\Entity\Transaction;
use App\Stock\StockContainer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class SummaryReport
{
    /**
     * @var \App\Stock\StockContainer
     */
    private $stocks;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Entity\Portfolio
     */
    private $portfolio;

    /**
     * SummaryReport constructor.
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Entity\Portfolio $portfolio
     */
    public function __construct(EntityManagerInterface $entityManager, Portfolio $portfolio)
    {
        $this->entityManager = $entityManager;
        $this->portfolio = $portfolio;

        $this->initStocks();
    }

    /**
     * @return \App\Entity\Portfolio
     */
    public function getPortfolio(): Portfolio
    {
        return $this->portfolio;
    }

    /**
     * @return \App\Stock\StockContainer
     */
    public function getStocks(): StockContainer
    {
        return $this->stocks;
    }

    /**
     * @return float
     */
    public function getCurrentCost(): float
    {
        $cost = 0;

        foreach ($this->stocks as $stock) {
            $cost += $stock->getCurrentTotal();
        }

        return $cost;
    }

    /**
     * @return float
     */
    public function getTotalExpenses(): float
    {
        $expenses = 0;

        foreach ($this->stocks as $stock) {
            $expenses += $stock->getTotal();
        }

        return $expenses;
    }

    /**
     * @return float
     */
    public function getEarnings(): float
    {
        return $this->getCurrentCost() - $this->getTotalExpenses();
    }

    /**
     * @return float
     */
    public function getEarningsInPercent(): float
    {
        return ($this->getCurrentCost() * 100 / $this->getTotalExpenses()) - 100;
    }

    /**
     * @return float
     */
    public function getAverageYearIncomeInPercents(): float
    {
        $firstTransaction = $this->entityManager->getRepository(Transaction::class)
            ->findFirstTransactionInPortfolio($this->portfolio);

        if (null === $firstTransaction) {
            return 0;
        }

        $today = new DateTime('today');
        $firstTransactionDay = $firstTransaction->getCreatedAt();
        $daysFromFirstTransaction = $today->diff($firstTransactionDay)->days;

        return 365 * $this->getEarningsInPercent() / $daysFromFirstTransaction;
    }

    private function initStocks(): void
    {
        $this->stocks = $this->entityManager->getRepository(Transaction::class)
            ->getStocksInPortfolio($this->portfolio);
    }
}