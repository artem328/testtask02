<?php

namespace App\Finance\Api;

interface QuoteInterface extends BaseQuoteInterface
{
    /**
     * @return float
     */
    public function getMarketPrice(): float;

    /**
     * @return float
     */
    public function getAskPrice(): float;

    /**
     * @return int
     */
    public function getAskSize(): int;

    /**
     * @return float
     */
    public function getBidPrice(): float;

    /**
     * @return int
     */
    public function getBidSize(): int;

    /**
     * @return string
     */
    public function getCurrency(): string;
}