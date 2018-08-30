<?php

declare(strict_types=1);

namespace Doctrine\Website\Builder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use function assert;
use function end;
use function explode;
use function file_get_contents;
use function in_array;
use function is_string;
use function preg_match;
use function preg_replace;
use function str_replace;
use function strrpos;
use function substr;

class SourceFileRepository
{
    /** @var string */
    private $rootDir;

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return SourceFile[]
     */
    public function getFiles(string $buildDir, string $inPath = 'source') : array
    {
        $finder = new Finder();

        $finder
            ->in($this->rootDir . '/' . $inPath)
            ->files();

        $files = [];

        foreach ($finder as $splFileInfo) {
            $path = $splFileInfo->getRealPath();
            assert(is_string($path));

            $contents = file_get_contents($path);
            assert(is_string($contents));

            $extension = $this->getExtension($path);

            $writePath = $this->getWritePath($buildDir, $path, $extension);

            $sourceFileParameters = $this->createSourceFileParameters(
                $buildDir,
                $writePath,
                $contents
            );

            $writePath = $buildDir . $sourceFileParameters->getParameter('url');

            $contents = $this->stripFileParameters($contents);

            $files[] = new SourceFile(
                $extension,
                $path,
                $writePath,
                $contents,
                $sourceFileParameters
            );
        }

        return $files;
    }

    private function getWritePath(string $buildDir, string $path, string $extension) : string
    {
        $writePath = $buildDir . str_replace($this->rootDir . '/source', '', $path);

        if (in_array($extension, ['md', 'rst'], true)) {
            $writePath = substr($writePath, 0, (int) strrpos($writePath, '.')) . '.html';
        }

        return $writePath;
    }

    private function getExtension(string $path) : string
    {
        $e = explode('.', $path);

        return end($e);
    }

    private function stripFileParameters(string $contents) : string
    {
        return preg_replace('/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s', '$2', $contents);
    }

    private function createSourceFileParameters(
        string $buildDir,
        string $writePath,
        string $string
    ) : SourceFileParameters {
        $parameters = [];

        if (preg_match('/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s', $string, $matches) > 0) {
            if (preg_match('/^(\s*[-]+\s*|\s*)$/', $matches[1]) === 0) {
                $parameters = Yaml::parse($matches[1], 1);
            }
        }

        if (! isset($parameters['layout'])) {
            $parameters['layout'] = 'default';
        }

        $parameters['url'] = $this->getUrl($buildDir, $writePath, $parameters);

        return new SourceFileParameters($parameters);
    }

    /**
     * @param mixed[] $parameters
     */
    private function getUrl(string $buildDir, string $writePath, array $parameters) : string
    {
        $permalink = $parameters['permalink'] ?? '';

        if ($permalink !== '' && $permalink !== 'none') {
            return $permalink;
        }

        return str_replace($buildDir, '', $writePath);
    }
}
