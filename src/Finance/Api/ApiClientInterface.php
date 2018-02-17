<?php

namespace App\Finance\Api;

use DateTime;

interface ApiClientInterface
{

    /**
     * @return string
     */
    public function getSourceName(): string;

    /**
     * @param string $search
     * @return \App\Finance\Api\SearchResultInterface[]
     */
    public function search(string $search): array;

    /**
     * @param string $symbol
     * @return \App\Finance\Api\QuoteInterface|null
     * @throws \App\Finance\Api\ApiException
     */
    public function getQuote(string $symbol): ?QuoteInterface;

    /**
     * @param array $symbols
     * @return \App\Finance\Api\QuoteInterface[]
     * @throws \App\Finance\Api\ApiException
     */
    public function getQuotes(array $symbols): array;

    /**
     * @param string $symbol
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @return \App\Finance\Api\HistoricalDataInterface[]
     * @throws \App\Finance\Api\ApiException
     */
    public function getHistoricalData(string $symbol, DateTime $dateStart, DateTime $dateEnd): array;
}