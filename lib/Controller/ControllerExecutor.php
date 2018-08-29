<?php

declare(strict_types=1);

namespace Doctrine\Website\Controller;

use Doctrine\Website\Builder\SourceFile;
use ReflectionClass;
use RuntimeException;
use function sprintf;

class ControllerExecutor
{
    /** @var ControllerProvider */
    private $controllerProvider;

    public function __construct(ControllerProvider $controllerProvider)
    {
        $this->controllerProvider = $controllerProvider;
    }

    /**
     * @return mixed[]
     */
    public function execute(SourceFile $sourceFile) : array
    {
        [$className, $methodName] = $sourceFile->getParameter('controller');

        $controller = $this->controllerProvider->getController($className);

        $reflectionMethod = (new ReflectionClass($controller))->getMethod($methodName);

        $controllerResult = $reflectionMethod->invokeArgs($controller, [$sourceFile]);

        if (! $controllerResult instanceof ControllerResult) {
            throw new RuntimeException(sprintf(
                'Controller %s did not return a %s instance.',
                $className,
                ControllerResult::class
            ));
        }

        return $controllerResult->getResult();
    }
}
