<?php

namespace App\DataFixtures;

use App\Entity\Portfolio;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PortfolioFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)
            ->findAll();

        foreach ($users as $user) {
            $portfolios = rand(0, 3);

            for ($i = 0; $i < $portfolios; $i++) {
                $portfolio = new Portfolio();

                $portfolio->setName('Portfolio '.($i + 1));
                $portfolio->setUser($user);
                $manager->persist($portfolio);
            }
        }

        $manager->flush();
    }
}