<?php

declare(strict_types=1);

namespace Doctrine\Website\Sitemap;

use DateTimeImmutable;
use Iterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplHeap;
use function array_merge;
use function assert;
use function file_exists;
use function filemtime;
use function is_int;
use function str_replace;
use function strcmp;

class SitemapPageRepository
{
    /** @var string */
    private $sourcePath;

    public function __construct(string $sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    /**
     * @return SitemapPage[]
     */
    public function findAll() : array
    {
        return array_merge(
            [new SitemapPage('/', new DateTimeImmutable())],
            $this->getUrlsFromFiles('projects'),
            $this->getUrlsFromFiles('api')
        );
    }

    /**
     * @return SitemapPage[]
     */
    private function getUrlsFromFiles(string $path) : array
    {
        $path = $this->sourcePath . '/' . $path;

        if (! file_exists($path)) {
            return [];
        }

        $it = new RecursiveDirectoryIterator(
            $path,
            RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::CURRENT_AS_PATHNAME
        );

        $it = new RecursiveIteratorIterator($it);

        // Sorting the results so we get a consistent order in the sitemap
        $sortedIterator = new class($it) extends SplHeap {
            public function __construct(Iterator $iterator)
            {
                foreach ($iterator as $item) {
                    $this->insert($item);
                }
            }

            public function compare(string $b, string $a) : int
            {
                return strcmp($a, $b);
            }
        };

        $urls = [];
        foreach ($sortedIterator as $file) {
            $url = str_replace($this->sourcePath, '', $file);

            $timestamp = filemtime($file);
            assert(is_int($timestamp));

            $date = (new DateTimeImmutable())->setTimestamp($timestamp);

            $urls[] = new SitemapPage($url, $date);
        }

        return $urls;
    }
}
