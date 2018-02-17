<?php

namespace App\Finance\Api\YahooFinance;

use App\Finance\Api\ApiClientInterface;
use App\Finance\Api\ApiException;
use App\Finance\Api\QuoteInterface;
use DateTime;
use Exception;
use Scheb\YahooFinanceApi\ApiClient as SchebApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;

class ApiClient implements ApiClientInterface
{

    const SOURCE_NAME = 'Yahoo Finance';

    /**
     * @var \Scheb\YahooFinanceApi\ApiClient
     */
    private $client;

    /**
     * ApiClient constructor.
     */
    public function __construct()
    {
        $this->client = ApiClientFactory::createApiClient();
    }

    /**
     * @return \Scheb\YahooFinanceApi\ApiClient
     */
    public function getClient(): SchebApiClient
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getSourceName(): string
    {
        return self::SOURCE_NAME;
    }

    /**
     * @param string $symbol
     * @return \App\Finance\Api\QuoteInterface|null
     * @throws \App\Finance\Api\ApiException
     */
    public function getQuote(string $symbol): ?QuoteInterface
    {
        try {
            $quote = $this->client->getQuote($symbol);
        } catch (Exception $exception) {
            throw new ApiException("Couldn't get quote for symbol [$symbol]", 0, $exception);
        }

        return null !== $quote ? new Quote($quote) : null;
    }

    /**
     * @param array $symbols
     * @return \App\Finance\Api\QuoteInterface[]
     * @throws \App\Finance\Api\ApiException
     */
    public function getQuotes(array $symbols): array
    {
        try {
            $quotes = $this->client->getQuotes($symbols);
        } catch (Exception $exception) {
            $symbolsString = implode(', ', $symbols);
            throw new ApiException("Couldn't get quotes for symbols [$symbolsString]", 0, $exception);
        }
        $results = [];

        foreach ($quotes as $quote) {
            $results[] = new Quote($quote);
        }

        return $results;
    }

    /**
     * @param string $symbol
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     * @return \App\Finance\Api\HistoricalDataInterface[]
     * @throws \App\Finance\Api\ApiException
     */
    public function getHistoricalData(string $symbol, DateTime $dateStart, DateTime $dateEnd): array
    {
        $quote = $this->getQuote($symbol);

        try {
            $historicalDataItems = $this->client->getHistoricalData(
                $symbol,
                SchebApiClient::INTERVAL_1_DAY,
                $dateStart,
                $dateEnd
            );
        } catch (Exception $exception) {
            $date = $dateStart == $dateEnd ? 'of '.$dateStart->format('Y-m-d') : 'from '.$dateStart->format('Y-m-d').' till '.$dateEnd->format('Y-m-d');
            throw new ApiException("Couldn't get historical data for symbol [$symbol] $date", 0, $exception);
        }
        $results = [];

        foreach ($historicalDataItems as $historicalData) {
            $results[] = new HistoricalData($historicalData, $quote->getSymbol(), $quote->getName());
        }

        return $results;
    }

    /**
     * @param string $search
     * @return \App\Finance\Api\SearchResultInterface[]
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function search(string $search): array
    {
        $searchResults = $this->client->search($search);
        $results = [];

        foreach ($searchResults as $searchResult) {
            $results[] = new SearchResult($searchResult);
        }

        return $results;
    }
}