<?php

namespace App\Tests;

use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @var \App\Entity\User
     */
    private $user;

    public function testGetSetUsername(): void
    {
        $this->user->setUsername('username');

        $this->assertEquals('username', $this->user->getUsername());

        $this->user->setUsername('username2');

        $this->assertEquals('username2', $this->user->getUsername());
    }

    public function testGetSetPlainPassword(): void
    {
        $this->user->setPlainPassword('password');

        $this->assertEquals('password', $this->user->getPlainPassword());

        $this->user->setPlainPassword('anotherpassword');

        $this->assertEquals('anotherpassword', $this->user->getPlainPassword());
    }

    public function testGetSetName(): void
    {
        $this->user->setName('John Doe');

        $this->assertEquals('John Doe', $this->user->getName());

        $this->user->setName('Eric Widget');

        $this->assertEquals('Eric Widget', $this->user->getName());
    }

    public function testGetSetPassword(): void
    {
        $this->user->setPassword('hashedpassword');

        $this->assertEquals('hashedpassword', $this->user->getPassword());

        $this->user->setPassword('anotherhashedpassword');

        $this->assertEquals('anotherhashedpassword', $this->user->getPassword());
    }

    public function testGetSetCreatedAt(): void
    {
        $date = new DateTime();

        $this->user->setCreatedAt($date);

        $this->assertEquals($date, $this->user->getCreatedAt());

        $anotherDate = new DateTime('2000-02-22');

        $this->user->setCreatedAt($anotherDate);

        $this->assertEquals(new DateTime('2000-02-22'), $this->user->getCreatedAt());
    }

    public function testGetSetUpdatedAt(): void
    {
        $date = new DateTime();

        $this->user->setUpdatedAt($date);

        $this->assertEquals($date, $this->user->getUpdatedAt());

        $anotherDate = new DateTime('2000-02-22');

        $this->user->setUpdatedAt($anotherDate);

        $this->assertEquals(new DateTime('2000-02-22'), $this->user->getUpdatedAt());
    }

    public function testGetRoles(): void
    {
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
    }

    public function testEraseCredentials(): void
    {
        $password = 'secret';

        $this->user->setPlainPassword($password);

        $this->assertEquals($password, $this->user->getPlainPassword());

        $this->user->eraseCredentials();

        $this->assertEquals(null, $this->user->getPlainPassword());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->user = new User();
    }
}