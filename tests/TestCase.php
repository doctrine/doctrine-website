<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\Website\Application;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class TestCase extends BaseTestCase
{
    /** @var ContainerBuilder */
    private $container;

    protected function getContainer() : ContainerBuilder
    {
        if ($this->container === null) {
            $this->container = Application::getContainer('test');
        }

        return $this->container;
    }
}
