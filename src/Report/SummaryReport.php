<?php

namespace App\Report;

use App\Stock\StockContainer;

class SummaryReport
{
    /**
     * @var \App\Stock\StockContainer
     */
    private $stocks;

    /**
     * SummaryReport constructor.
     * @param \App\Stock\StockContainer $stocks
     */
    public function __construct(StockContainer $stocks)
    {
        $this->stocks = $stocks;
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
}