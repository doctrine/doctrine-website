<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function array_map;
use function array_values;
use function file_get_contents;
use function is_dir;
use function iterator_to_array;
use function sprintf;
use function trim;

class RSTFileRepository
{
    /**
     * @throws InvalidArgumentException
     */
    public function getFileContents(string $path) : string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Could not get contents of file %s', $path));
        }

        return trim($contents);
    }

    /**
     * @return string[]
     */
    public function findMetaFiles(string $path) : array
    {
        $finder = $this->getFilesFinder($path)->name('meta.php');

        return $this->finderToArray($finder);
    }

    /**
     * @return string[]
     */
    public function findFiles(string $path) : array
    {
        if (! is_dir($path)) {
            return [];
        }

        return $this->finderToArray($this->getFilesFinder($path));
    }

    /**
     * @return string[]
     */
    public function getSourceFiles(string $path) : array
    {
        if (! is_dir($path)) {
            return [];
        }

        $finder = $this->getFilesFinder($path);

        $finder->name('*.rst');
        $finder->notName('toc.rst');

        return $this->finderToArray($finder);
    }

    private function getFilesFinder(string $path) : Finder
    {
        $finder = new Finder();
        $finder->in($path)->files();

        return $finder;
    }

    /**
     * @return string[]
     */
    private function finderToArray(Finder $finder) : array
    {
        return array_values(array_map(static function (SplFileInfo $file) {
            return $file->getRealPath();
        }, iterator_to_array($finder)));
    }
}
