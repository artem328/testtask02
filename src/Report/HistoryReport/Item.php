<?php

namespace App\Report\HistoryReport;

use App\Stock\Stock;
use App\Stock\StockContainer;
use DateTime;

class Item
{
    /**
     * @var \App\Stock\StockContainer
     */
    private $stocks;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var \App\Report\HistoryReport\Item
     */
    private $previous;

    /**
     * @var \App\Report\HistoryReport\Item
     */
    private $next;

    /**
     * Item constructor.
     * @param \App\Stock\StockContainer $stocks
     * @param \DateTime $date
     */
    public function __construct(StockContainer $stocks, DateTime $date)
    {
        $this->stocks = $stocks;
        $this->date = $date;
    }

    public function getStocks(): StockContainer
    {
        return $this->stocks;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return \App\Report\HistoryReport\Item|null
     */
    public function getPrevious(): ?Item
    {
        return $this->previous;
    }

    /**
     * @param \App\Report\HistoryReport\Item|null $previous
     */
    public function setPrevious(?Item $previous): void
    {
        $this->previous = $previous;
    }

    /**
     * @return bool
     */
    public function hasPrevious(): bool
    {
        return null !== $this->previous;
    }

    /**
     * @return \App\Report\HistoryReport\Item|null
     */
    public function getNext(): ?Item
    {
        return $this->next;
    }

    /**
     * @param \App\Report\HistoryReport\Item|null $next
     */
    public function setNext(?Item $next): void
    {
        $this->next = $next;
    }

    /**
     * @return bool
     */
    public function hasNext(): bool
    {
        return null !== $this->next;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        $today = new DateTime('today');

        if ($today == $this->getDate()) {
            return $this->stocks->reduce(function (float $value, Stock $stock) {
                if ($quote = $stock->getQuote()) {
                    return $value + ($stock->getQuantity() * $quote->getMarketPrice());
                }

                return $value;
            }, 0);
        }

        return $this->stocks->reduce(function (float $value, Stock $stock) {

            $item = $this;

            do {
                $historicalData = $stock->getHistoricalData($item->getDate());
                $item = $item->getPrevious();
            } while (!$historicalData && null !== $item);

            if (!$historicalData) {
                return 0;
            }

            return $value + ($stock->getQuantity() * $historicalData->getAveragePrice());
        }, 0);
    }

    /**
     * @param \App\Report\HistoryReport\Item $item
     * @return float
     */
    public function getCostDiff(Item $item): float
    {
        return $this->getCost() - $item->getCost();
    }

    /**
     * @param \App\Report\HistoryReport\Item $item
     * @return float
     */
    public function getCostDiffInPercent(Item $item): float
    {
        return 0.0 === $this->getCost() ?
            100 :
            ($item->getCost() / $this->getCost() * 100) - 100;
    }

    /**
     * @return float
     */
    public function getCostDiffWithNext(): float
    {
        if ($this->getNext()) {
            return $this->getCostDiff($this->getNext());
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getCostDiffInPercentWithNext(): float
    {
        if ($this->getNext()) {
            return $this->getCostDiffInPercent($this->getNext());
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getCostDiffWithPrevious(): float
    {
        if ($this->getPrevious()) {
            return $this->getCostDiff($this->getPrevious());
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getCostDiffInPercentWithPrevious(): float
    {
        if ($this->getPrevious()) {
            return $this->getPrevious()->getCostDiffInPercent($this);
        }

        return 0;
    }
}