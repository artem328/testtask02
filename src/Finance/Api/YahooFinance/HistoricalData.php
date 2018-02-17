<?php

namespace App\Finance\Api\YahooFinance;

use App\Finance\Api\HistoricalDataInterface;
use DateTime;
use Scheb\YahooFinanceApi\Results\HistoricalData as SchebHistoricalData;

class HistoricalData implements HistoricalDataInterface
{

    /**
     * @var \Scheb\YahooFinanceApi\Results\HistoricalData
     */
    private $historicalData;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $symbol;

    /**
     * HistoricalData constructor.
     * @param \Scheb\YahooFinanceApi\Results\HistoricalData $historicalData
     * @param string $symbol
     * @param string $name
     */
    public function __construct(SchebHistoricalData $historicalData, string $symbol, string $name)
    {
        $this->historicalData = $historicalData;
        $this->symbol = $symbol;
        $this->name = $name;
    }

    /**
     * @return \Scheb\YahooFinanceApi\Results\HistoricalData
     */
    public function getHistoricalData(): SchebHistoricalData
    {
        return $this->historicalData;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return float
     */
    public function getOpenPrice(): float
    {
        return $this->historicalData->getOpen();
    }

    /**
     * @return float
     */
    public function getClosePrice(): float
    {
        return $this->historicalData->getClose();
    }

    /**
     * @return float
     */
    public function getLowPrice(): float
    {
        return $this->historicalData->getLow();
    }

    /**
     * @return float
     */
    public function getHighPrice(): float
    {
        return $this->historicalData->getHigh();
    }

    /**
     * @return \DateTime
     */
    public function getDate(): DateTime
    {
        return $this->historicalData->getDate();
    }

    /**
     * @return float
     */
    public function getAveragePrice(): float
    {
        return $this->getLowPrice() + $this->getHighPrice() + $this->getOpenPrice() + $this->getClosePrice() / 4;
    }
}