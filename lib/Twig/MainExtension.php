<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Doctrine\Website\Assets\AssetIntegrityGenerator;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Parsedown;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use function assert;
use function file_get_contents;
use function is_int;
use function is_string;
use function realpath;
use function sha1;
use function sprintf;
use function strlen;
use function strrpos;
use function substr;

class MainExtension extends Twig_Extension
{
    /** @var Parsedown */
    private $parsedown;

    /** @var AssetIntegrityGenerator */
    private $assetIntegrityGenerator;

    /** @var string */
    private $sourceDir;

    /** @var string */
    private $webpackBuildDir;

    public function __construct(Parsedown $parsedown, AssetIntegrityGenerator $assetIntegrityGenerator, string $sourceDir, string $webpackBuildDir)
    {
        $this->parsedown               = $parsedown;
        $this->assetIntegrityGenerator = $assetIntegrityGenerator;
        $this->sourceDir               = $sourceDir;
        $this->webpackBuildDir         = $webpackBuildDir;
    }

    /**
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('get_search_box_placeholder', [$this, 'getSearchBoxPlaceholder']),
            new Twig_SimpleFunction('get_asset_url', [$this, 'getAssetUrl']),
            new Twig_SimpleFunction('get_webpack_asset_url', [$this, 'getWebpackAssetUrl']),
            new Twig_SimpleFunction('get_asset_integrity', [$this->assetIntegrityGenerator, 'getAssetIntegrity']),
            new Twig_SimpleFunction('get_webpack_asset_integrity', [$this->assetIntegrityGenerator, 'getWebpackAssetIntegrity']),
        ];
    }

    /**
     * @return Twig_SimpleFilter[]
     */
    public function getFilters() : array
    {
        return [
            new Twig_SimpleFilter('markdown', [$this->parsedown, 'text'], ['is_safe' => ['html']]),
            new Twig_SimpleFilter('truncate', [$this, 'truncate']),
        ];
    }

    public function getSearchBoxPlaceholder(?Project $project = null, ?ProjectVersion $projectVersion = null) : string
    {
        if ($project !== null && $projectVersion !== null) {
            return 'Search ' . $project->getShortName() . ' ' . $projectVersion->getName();
        }

        if ($project !== null) {
            return 'Search ' . $project->getShortName();
        }

        return 'Search';
    }

    public function getAssetUrl(string $path, string $siteUrl, ?string $rootPath = null) : string
    {
        return $siteUrl . $path . '?' . $this->getAssetCacheBuster($path, $rootPath ?? $this->sourceDir);
    }

    public function getWebpackAssetUrl(string $path, string $siteUrl) : string
    {
        return $this->getAssetUrl($path, $siteUrl . '/frontend', $this->webpackBuildDir);
    }

    public function truncate(string $string, int $limit, string $separator = '...') : string
    {
        if (strlen($string) > $limit) {
            $newlimit = $limit - strlen($separator);

            $truncatedString = substr($string, 0, $newlimit + 1);

            $lastSpacePosition = strrpos($truncatedString, ' ');
            assert(is_int($lastSpacePosition));

            return substr($truncatedString, 0, $lastSpacePosition) . $separator;
        }

        return $string;
    }

    private function getAssetCacheBuster(string $path, string $rootPath) : string
    {
        $assetPath = realpath($rootPath . '/' . $path);
        assert(is_string($assetPath), sprintf('Failed to determine the path for the asset "%s"', $rootPath . '/' . $path));

        $contents = file_get_contents($assetPath);
        assert(is_string($contents), sprintf('Failed to load the asset located at "%s"', $rootPath . '/' . $path));

        return substr(sha1($contents), 0, 6);
    }
}
