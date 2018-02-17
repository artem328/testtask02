<?php

namespace App\Stock;

use App\Entity\Portfolio;
use App\Finance\Api\HistoricalDataInterface;
use App\Finance\Api\QuoteInterface;
use DateTime;

class Stock
{
    /**
     * @var string
     */
    private $symbol;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \App\Entity\Portfolio
     */
    private $portfolio;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var float
     */
    private $total;

    /**
     * @var \App\Finance\Api\QuoteInterface
     */
    private $quote;

    /**
     * @var \App\Finance\Api\HistoricalDataInterface[]
     */
    private $historicalData = [];

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \App\Entity\Portfolio
     */
    public function getPortfolio(): Portfolio
    {
        return $this->portfolio;
    }

    /**
     * @param \App\Entity\Portfolio $portfolio
     */
    public function setPortfolio(Portfolio $portfolio): void
    {
        $this->portfolio = $portfolio;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return \App\Finance\Api\QuoteInterface|null
     */
    public function getQuote(): ?QuoteInterface
    {
        return $this->quote;
    }

    /**
     * @param \App\Finance\Api\QuoteInterface $quote
     */
    public function setQuote(QuoteInterface $quote): void
    {
        $this->quote = $quote;
    }

    /**
     * @return float
     */
    public function getCurrentTotal(): float
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getMarketPrice() * $this->getQuantity();
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    /**
     * @param \App\Finance\Api\HistoricalDataInterface $historicalData
     */
    public function addHistoricalData(HistoricalDataInterface $historicalData)
    {
        $this->historicalData[$historicalData->getDate()->format('Y-m-d')] = $historicalData;
    }

    /**
     * @param \DateTime $date
     * @return \App\Finance\Api\HistoricalDataInterface|null
     */
    public function getHistoricalData(DateTime $date): ?HistoricalDataInterface
    {
        return $this->hasHistoricalData($date) ? $this->historicalData[$date->format('Y-m-d')] : null;
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function hasHistoricalData(DateTime $date): bool
    {
        return isset($this->historicalData[$date->format('Y-m-d')]);
    }

    /**
     * @return \App\Finance\Api\HistoricalDataInterface[]
     */
    public function getAllHistoricalData(): array
    {
        return $this->historicalData;
    }
}