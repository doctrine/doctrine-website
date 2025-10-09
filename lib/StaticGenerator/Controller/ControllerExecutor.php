<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Controller;

use Doctrine\Website\StaticGenerator\SourceFile\SourceFile;
use RuntimeException;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use function assert;
use function call_user_func_array;
use function is_callable;
use function sprintf;

class ControllerExecutor
{
    public function __construct(
        private ControllerProvider $controllerProvider,
        private ArgumentResolver $argumentResolver,
    ) {
    }

    public function execute(SourceFile $sourceFile): Response
    {
        $controller = $sourceFile->getController();

        if ($controller === null) {
            throw new RuntimeException('SourceFile::getController() should not return null here.');
        }

        [$className, $methodName] = $controller;

        $controller = $this->controllerProvider->getController($className);

        $callable = [$controller, $methodName];
        assert(is_callable($callable));

        $arguments = $this->argumentResolver->getArguments(
            $sourceFile->getRequest(),
            $callable,
        );

        $controllerResult = call_user_func_array($callable, $arguments);

        if (! $controllerResult instanceof Response) {
            throw new RuntimeException(sprintf(
                'Controller %s did not return a %s instance.',
                $className,
                Response::class,
            ));
        }

        return $controllerResult;
    }
}
