<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

use Doctrine\Website\StaticGenerator\Request\RequestCollectionProvider;
use Doctrine\Website\StaticGenerator\Routing\Router;
use Symfony\Component\Routing\Route;

use function array_filter;
use function assert;
use function is_string;

class SourceFileRouteReader implements SourceFileReader
{
    public function __construct(
        private Router $router,
        private RequestCollectionProvider $requestCollectionProvider,
        private SourceFileFactory $sourceFileFactory,
    ) {
    }

    public function getSourceFiles(string $buildDir = ''): SourceFiles
    {
        $sourceFiles = [];

        foreach ($this->getRoutesWithProvider() as $routeName => $route) {
            assert(is_string($routeName));

            [$className, $methodName] = $route->getDefault('_provider');

            $requestCollection = $this->requestCollectionProvider->getRequestCollection(
                $className,
                $methodName,
            );

            foreach ($requestCollection->getRequests() as $request) {
                $sourcePath = $this->router->generate($routeName, $request);

                $sourceFiles[] = $this->sourceFileFactory->createSourceFile(
                    $buildDir,
                    $sourcePath,
                );
            }
        }

        return new SourceFiles($sourceFiles);
    }

    /** @return Route[] */
    private function getRoutesWithProvider(): array
    {
        return array_filter($this->router->getRouteCollection()->all(), static function (Route $route): bool {
            return $route->getDefault('_provider') !== null;
        });
    }
}
