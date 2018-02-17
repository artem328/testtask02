<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var array
     */
    private $firstNames = [
        'John',
        'Hermann',
        'Eric',
        'Dianne',
        'Alan',
        'Cecil',
        'Valentino',
        'Manuel',
        'Bailey',
        'Lurch',
    ];

    /**
     * @var array
     */
    private $lastNames = [
        'Doe',
        'P. Fant',
        'Widget',
        'Ameter',
        'Fresco',
        'Hipplington-Shoreditch',
        'Morose',
        'Internetiquette',
        'Wonger',
        'Schpellchek',
    ];

    /**
     * UserFixtures constructor.
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $password = 'pass123';

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setUsername('user'.($i + 1));
            $user->setName($this->getName());
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return implode(' ',
            [
                $this->firstNames[rand(0, count($this->firstNames) - 1)],
                $this->lastNames[rand(0, count($this->lastNames) - 1)],
            ]
        );
    }
}