<?php

namespace App\Tests;

use App\Entity\Portfolio;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class PortfolioTest extends TestCase
{
    /**
     * @var \App\Entity\Portfolio
     */
    private $portfolio;

    public function testGetSetName(): void
    {
        $this->portfolio->setName('Portfolio 1');

        $this->assertEquals('Portfolio 1', $this->portfolio->getName());

        $this->portfolio->setName('Portfolio 2');

        $this->assertEquals('Portfolio 2', $this->portfolio->getName());
    }

    public function testGetSetCreatedAt(): void
    {
        $date = new DateTime();

        $this->portfolio->setCreatedAt($date);

        $this->assertEquals($date, $this->portfolio->getCreatedAt());

        $anotherDate = new DateTime('2000-02-22');

        $this->portfolio->setCreatedAt($anotherDate);

        $this->assertEquals(new DateTime('2000-02-22'), $this->portfolio->getCreatedAt());
    }

    public function testGetSetUpdatedAt(): void
    {
        $date = new DateTime();

        $this->portfolio->setUpdatedAt($date);

        $this->assertEquals($date, $this->portfolio->getUpdatedAt());

        $anotherDate = new DateTime('2000-02-22');

        $this->portfolio->setUpdatedAt($anotherDate);

        $this->assertEquals(new DateTime('2000-02-22'), $this->portfolio->getUpdatedAt());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->portfolio = new Portfolio();
    }
}