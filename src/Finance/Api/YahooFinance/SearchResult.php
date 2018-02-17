<?php

namespace App\Finance\Api\YahooFinance;

use App\Finance\Api\SearchResultInterface;
use Scheb\YahooFinanceApi\Results\SearchResult as SchebSearchResult;

class SearchResult implements SearchResultInterface
{
    /**
     * @var \Scheb\YahooFinanceApi\Results\SearchResult
     */
    private $searchResult;

    /**
     * SearchResult constructor.
     * @param \Scheb\YahooFinanceApi\Results\SearchResult $searchResult
     */
    public function __construct(SchebSearchResult $searchResult)
    {
        $this->searchResult = $searchResult;
    }

    /**
     * @return \Scheb\YahooFinanceApi\Results\SearchResult
     */
    public function getSearchResult(): SchebSearchResult
    {
        return $this->searchResult;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->searchResult->getName();
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->searchResult->getSymbol();
    }
}