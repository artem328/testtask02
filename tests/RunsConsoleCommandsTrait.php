<?php

namespace App\Tests;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

trait RunsConsoleCommandsTrait
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Console\Application
     */
    private static $application;

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Console\Application
     */
    protected static function getApplication(): Application
    {
        if (null === self::$application) {
            $kernel = new Kernel('test', true);

            self::$application = new Application($kernel);
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    /**
     * @param string $command
     * @param array $options
     * @return int
     * @throws \Exception
     */
    protected function runConsoleCommand(string $command, array $options = []): int
    {
        $arguments = [
            'command' => $command,
        ];

        $arguments = array_merge($options, $arguments);

        return static::getApplication()->run(new ArrayInput($arguments));
    }

}