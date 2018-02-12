<?php

namespace App\Tests;

/**
 * Trait RestoresDatabaseTrait
 * @package App\Tests
 * @mixin \App\Tests\RunsConsoleCommandsTrait
 */
trait RestoresDatabaseTrait
{
    /**
     * @throws \Exception
     */
    protected function restoreDatabase(): void
    {
        $args = [
            '--force' => true,
            '--env' => 'test',
        ];

        $this->runConsoleCommand('doctrine:schema:drop', $args);
        $this->runConsoleCommand('doctrine:schema:update', $args);
    }

}