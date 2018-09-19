<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use DateTimeImmutable;
use Doctrine\Website\DataSource\DataSource;
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

class SitemapPages implements DataSource
{
    /** @var string */
    private $sourcePath;

    public function __construct(string $sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    /**
     * @return mixed[][]
     */
    public function getData() : array
    {
        return array_merge(
            [
                [
                    'url' => '/',
                    'date' => new DateTimeImmutable(),
                ],
            ],
            $this->getSitemapPagesDataFromFiles('projects'),
            $this->getSitemapPagesDataFromFiles('api')
        );
    }

    /**
     * @return mixed[][]
     */
    private function getSitemapPagesDataFromFiles(string $path) : array
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
                return $a <=> $b;
            }
        };

        $urls = [];
        foreach ($sortedIterator as $file) {
            $url = str_replace($this->sourcePath, '', $file);

            $timestamp = filemtime($file);
            assert(is_int($timestamp));

            $date = (new DateTimeImmutable())->setTimestamp($timestamp);

            $urls[] = [
                'url' => $url,
                'date' => $date,
            ];
        }

        return $urls;
    }
}
