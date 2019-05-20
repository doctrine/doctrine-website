<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\Website\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use function strpos;

final class BuildAllBootstrap
{
    private const COMMAND = 'build-all';

    public function __invoke() : void
    {
        $container = Application::getContainer('test');

        /** @var Application $application */
        $application = $container->get(Application::class);

        $consoleApplication = $application->getConsoleApplication();
        $consoleApplication->setAutoExit(false);

        $input = new ArrayInput([
            'command' => self::COMMAND,
        ]);

        $consoleApplication->find(self::COMMAND)
            ->run($input, new ConsoleOutput());
    }
}

// only execute this for phpunit
if (strpos($_SERVER['PHP_SELF'], 'phpunit') !== false) {
    (new BuildAllBootstrap())->__invoke();
}
