<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

use Doctrine\Website\StaticGenerator\Routing\Router;

use function assert;
use function file_get_contents;
use function in_array;
use function pathinfo;
use function str_replace;
use function strrpos;
use function substr;

use const PATHINFO_EXTENSION;

class SourceFileFactory
{
    private const array CONVERTED_HTML_EXTENSIONS = ['md', 'rst'];

    public function __construct(
        private Router $router,
        private SourceFileParametersFactory $sourceFileParametersFactory,
        private string $sourceDir,
    ) {
    }

    public function createSourceFileFromPath(
        string $buildDir,
        string $sourcePath,
    ): SourceFile {
        return $this->createSourceFile(
            $buildDir,
            $sourcePath,
            $this->getFileContents($sourcePath),
        );
    }

    public function createSourceFile(
        string $buildDir,
        string $sourcePath,
        string $contents = '',
    ): SourceFile {
        $sourceFileParameters = $this->sourceFileParametersFactory
            ->createSourceFileParameters($contents);

        $url       = $this->buildUrl($buildDir, $sourcePath, $sourceFileParameters->getAll());
        $writePath = $buildDir . $url;

        $sourceFileParameters->setParameter('url', $url);
        $sourceFileParameters->setParameter('writePath', $writePath);

        $route = $this->router->match($url);

        if ($route !== null) {
            $sourceFileParameters->merge($route);
        }

        return new SourceFile(
            $sourcePath,
            $contents,
            $sourceFileParameters,
        );
    }

    /** @param mixed[] $parameters */
    private function buildUrl(string $buildDir, string $sourcePath, array $parameters): string
    {
        $permalink = $parameters['permalink'] ?? '';

        if ($permalink !== '' && $permalink !== 'none') {
            return $permalink;
        }

        $writePath = $this->buildWritePath($buildDir, $sourcePath);

        return str_replace($buildDir, '', $writePath);
    }

    private function buildWritePath(string $buildDir, string $sourcePath): string
    {
        $writePath = $buildDir . str_replace($this->sourceDir, '', $sourcePath);

        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);

        if (in_array($extension, self::CONVERTED_HTML_EXTENSIONS, true)) {
            $writePath = substr($writePath, 0, (int) strrpos($writePath, '.')) . '.html';
        }

        return $writePath;
    }

    private function getFileContents(string $path): string
    {
        $contents = file_get_contents($path);
        assert($contents !== false);

        return $contents;
    }
}
