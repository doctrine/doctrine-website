<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Request;

use InvalidArgumentException;

use function assert;
use function call_user_func;
use function is_callable;
use function sprintf;

class RequestCollectionProvider
{
    /** @var object[] */
    private array $providers;

    /** @param object[] $providers */
    public function __construct(array $providers)
    {
        foreach ($providers as $provider) {
            $this->providers[$provider::class] = $provider;
        }
    }

    public function getRequestCollection(string $className, string $methodName): RequestCollection
    {
        if (! isset($this->providers[$className])) {
            throw new InvalidArgumentException(
                sprintf('Could not find request collection provider for class named %s', $className),
            );
        }

        $callable = [$this->providers[$className], $methodName];
        assert(is_callable($callable));

        return call_user_func($callable);
    }
}
