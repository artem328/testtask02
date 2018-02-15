<?php

namespace App\Finance\Api\YahooFinance;

use App\Finance\Api\HistoricalDataInterface;
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
        return '';
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return '';
    }
}