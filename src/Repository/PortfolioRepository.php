<?php

namespace App\Repository;

use App\Entity\Portfolio;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PortfolioRepository extends ServiceEntityRepository
{
    /**
     * PortfolioRepository constructor.
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Portfolio::class);
    }

    /**
     * @param \App\Entity\User $user
     * @return \App\Entity\Portfolio[]
     */
    public function findAllOfUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :user')->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

}
