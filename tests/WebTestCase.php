<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    use RunsConsoleCommandsTrait,
        RestoresDatabaseTrait;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->restoreDatabase();
    }

    /**
     * @throws \Exception
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreDatabase();
    }
}