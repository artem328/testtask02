<?php

namespace App\Repository;

use App\Entity\Portfolio;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @param \App\Entity\Portfolio $portfolio
     * @param string $symbol
     * @return int
     */
    public function countStocksInPortfolio(Portfolio $portfolio, string $symbol): int
    {
        $results = $this->createQueryBuilder('t')
            ->where('t.symbol = :symbol')->setParameter('symbol', $symbol)
            ->andWhere('t.portfolio = :portfolio')->setParameter('portfolio', $portfolio)
            ->andWhere('t.total > 0')
            ->groupBy('t.symbol')
            ->select('SUM(t.quantity) as owned_stocks')
            ->getQuery()
            ->execute();

        $result = $results[0] ?? [];

        return (int)$result['owned_stocks'] ?? 0;
    }
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('t')
            ->where('t.something = :value')->setParameter('value', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
