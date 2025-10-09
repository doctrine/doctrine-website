<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Controller;

use InvalidArgumentException;

use function sprintf;

class ControllerProvider
{
    /** @var object[] */
    private array $controllers;

    /** @param object[] $controllers */
    public function __construct(array $controllers)
    {
        foreach ($controllers as $controller) {
            $this->controllers[$controller::class] = $controller;
        }
    }

    public function getController(string $className): object
    {
        if (! isset($this->controllers[$className])) {
            throw new InvalidArgumentException(sprintf('Could not find controller class %s', $className));
        }

        return $this->controllers[$className];
    }
}
