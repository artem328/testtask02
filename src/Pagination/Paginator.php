<?php

namespace App\Pagination;

use Doctrine\ORM\Tools\Pagination\Paginator as BasePaginator;

class Paginator extends BasePaginator
{
    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var string
     */
    protected $pageParam = 'page';

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;

        $this->getQuery()->setFirstResult(($this->page - 1) * $this->getLimit());
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->getQuery()->getMaxResults();
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->getQuery()->setMaxResults($limit);

        // Update the query offset
        $this->setPage($this->page);
    }

    /**
     * @return string
     */
    public function getPageParam(): string
    {
        return $this->pageParam;
    }

    /**
     * @param string $pageParam
     */
    public function setPageParam(string $pageParam): void
    {
        $this->pageParam = $pageParam;
    }

    /**
     * @return int
     */
    public function getMaxPages(): int
    {
        return ceil($this->count() / $this->getLimit());
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @param int $page
     * @return string
     */
    public function generateUrl(int $page): string
    {
        $query = $this->getPageParam().'='.$page;

        return $this->getUrl().(strpos($this->getUrl(), '?') !== false ? '&' : '?').$query;
    }

}