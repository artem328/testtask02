<?php

namespace App\Controller;

use App\Pagination\Paginator;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\RequestStack;

trait HasPaginationTrait
{

    /**
     * @var string
     */
    protected static $pageParam = 'page';

    /**
     * @var int
     */
    protected static $entriesPerPage = 10;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**.
     * HasPaginationTrait constructor.
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param \Doctrine\ORM\Query $dql
     * @param int|null $page
     * @param int|null $limit
     * @param null|string $url
     *
     * @return \App\Pagination\Paginator
     */
    public function paginate(Query $dql, ?int $page = null, ?int $limit = null, ?string $url = null): Paginator
    {
        if (null === $page) {
            $page = $this->getPage();
        }

        if (null === $limit) {
            $limit = $this->getLimit();
        }

        if (null === $url) {
            $url = $this->request->getUri();
        }

        $paginator = new Paginator($dql);
        $paginator->setLimit($limit);
        $paginator->setPage($page);
        $paginator->setUrl($url);

        return $paginator;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return (int)$this->request->get(static::$pageParam, 1);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return static::$entriesPerPage;
    }
}