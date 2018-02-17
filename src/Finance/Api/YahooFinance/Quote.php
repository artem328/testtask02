<?php

namespace App\Finance\Api\YahooFinance;

use App\Finance\Api\QuoteInterface;
use Scheb\YahooFinanceApi\Results\Quote as SchebQuote;

class Quote implements QuoteInterface
{

    /**
     * @var \Scheb\YahooFinanceApi\Results\Quote
     */
    private $quote;

    /**
     * Quote constructor.
     * @param \Scheb\YahooFinanceApi\Results\Quote $quote
     */
    public function __construct(SchebQuote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return \Scheb\YahooFinanceApi\Results\Quote
     */
    public function getQuote(): SchebQuote
    {
        return $this->quote;
    }

    /**
     * @return float
     */
    public function getAskPrice(): float
    {
        return $this->quote->getAsk();
    }

    /**
     * @return float
     */
    public function getBidPrice(): float
    {
        return $this->quote->getBid();
    }

    /**
     * @return int
     */
    public function getAskSize(): int
    {
        return $this->quote->getAskSize();
    }

    /**
     * @return int
     */
    public function getBidSize(): int
    {
        return $this->quote->getBidSize();
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->quote->getCurrency();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->quote->getLongName();
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->quote->getSymbol();
    }

    /**
     * @return float
     */
    public function getMarketPrice(): float
    {
        return $this->quote->getRegularMarketPrice();
    }
}