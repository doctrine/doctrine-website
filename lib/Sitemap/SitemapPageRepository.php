<?php

declare(strict_types=1);

namespace Doctrine\Website\Sitemap;

use DateTimeImmutable;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function array_merge;
use function assert;
use function file_exists;
use function filemtime;
use function is_int;
use function is_string;
use function realpath;
use function str_replace;
use function strpos;

class SitemapPageRepository
{
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
    private function getUrlsFromFiles(string $path, string $extension = 'html') : array
    {
        $root = realpath(__DIR__ . '/../../source');
        assert(is_string($root));

        $path = $root . '/' . $path;

        if (! file_exists($path)) {
            return [];
        }

        $it = new RecursiveDirectoryIterator(
            $path,
            RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::CURRENT_AS_PATHNAME
        );

        $urls = [];
        foreach (new RecursiveIteratorIterator($it) as $file) {
            if (strpos($file, '.' . $extension) === false) {
                continue;
            }

            $url = str_replace($root, '', $file);

            $timestamp = filemtime($file);
            assert(is_int($timestamp));

            $date = (new DateTimeImmutable())->setTimestamp($timestamp);

            $urls[] = new SitemapPage($url, $date);
        }

        return $urls;
    }
}
