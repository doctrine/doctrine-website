<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Doctrine\Website\Projects\Project;
use Parsedown;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use function assert;
use function file_exists;
use function file_get_contents;
use function filemtime;
use function is_string;
use function realpath;
use function sha1;
use function str_replace;
use function strpos;
use function substr;

class MainExtension extends Twig_Extension
{
    /** @var Parsedown */
    private $parsedown;

    public function __construct(Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;
    }

    /**
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('get_search_box_placeholder', [$this, 'getSearchBoxPlaceholder']),
            new Twig_SimpleFunction('get_asset_url', [$this, 'getAssetUrl']),
            new Twig_SimpleFunction('get_docs_urls', [$this, 'getDocsUrls']),
            new Twig_SimpleFunction('get_api_docs_urls', [$this, 'getApiDocsUrls']),
        ];
    }

    /**
     * @return Twig_SimpleFilter[]
     */
    public function getFilters() : array
    {
        return [
            new Twig_SimpleFilter('markdown', [$this->parsedown, 'text']),
        ];
    }

    public function getSearchBoxPlaceholder(?Project $project = null, ?string $version = null) : string
    {
        $projectVersion = $project !== null && is_string($version)
            ? $project->getVersion($version)
            : null;

        if ($project !== null && $projectVersion !== null) {
            return 'Search ' . $project->getShortName() . ' ' . $projectVersion->getName();
        }

        if ($project !== null) {
            return 'Search ' . $project->getShortName();
        }

        return 'Search';
    }

    public function getAssetUrl(string $path, string $siteUrl) : string
    {
        return $siteUrl . $path . '?' . $this->getAssetCacheBuster($path);
    }

    /**
     * @return mixed[]
     */
    public function getDocsUrls() : array
    {
        return $this->getUrlsFromFiles('projects');
    }

    /**
     * @return mixed[]
     */
    public function getApiDocsUrls() : array
    {
        return $this->getUrlsFromFiles('api');
    }

    /**
     * @return mixed[]
     */
    private function getUrlsFromFiles(string $path, string $extension = 'html') : array
    {
        $root = realpath(__DIR__ . '/../../source');
        assert(is_string($root));

        $path = $root . '/' . $path;

        if (! file_exists($path)) {
            return [];
        }

        $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);

        $urls = [];
        foreach (new RecursiveIteratorIterator($it) as $file) {
            $path = (string) $file;

            if (strpos($path, '.' . $extension) === false) {
                continue;
            }

            $url = str_replace($root, '', $path);

            $urls[] = [
                'url' => $url,
                'date' => filemtime($path),
            ];
        }

        return $urls;
    }

    private function getAssetCacheBuster(string $path) : string
    {
        $assetPath = realpath(__DIR__ . '/../../source/' . $path);
        assert(is_string($assetPath));

        $contents = file_get_contents($assetPath);
        assert(is_string($contents));

        return substr(sha1($contents), 0, 6);
    }
}
