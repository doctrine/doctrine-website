<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Doctrine\Website\Assets\AssetIntegrityGenerator;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

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

class MainExtension extends AbstractExtension
{
    public function __construct(
        private Parsedown $parsedown,
        private AssetIntegrityGenerator $assetIntegrityGenerator,
        private string $sourceDir,
        private string $webpackBuildDir,
    ) {
    }

    /** {@inheritDoc} */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_search_box_placeholder', [$this, 'getSearchBoxPlaceholder']),
            new TwigFunction('get_asset_url', [$this, 'getAssetUrl']),
            new TwigFunction('get_webpack_asset_url', [$this, 'getWebpackAssetUrl']),
            new TwigFunction('get_asset_integrity', [$this->assetIntegrityGenerator, 'getAssetIntegrity']),
            new TwigFunction('get_webpack_asset_integrity', [$this->assetIntegrityGenerator, 'getWebpackAssetIntegrity']),
        ];
    }

    /** {@inheritDoc} */
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this->parsedown, 'text'], ['is_safe' => ['html']]),
            new TwigFilter('truncate', [$this, 'truncate']),
        ];
    }

    public function getSearchBoxPlaceholder(Project|null $project = null, ProjectVersion|null $projectVersion = null): string
    {
        if ($project !== null && $projectVersion !== null) {
            return 'Search ' . $project->getShortName() . ' ' . $projectVersion->getName();
        }

        if ($project !== null) {
            return 'Search ' . $project->getShortName();
        }

        return 'Search';
    }

    public function getAssetUrl(string $path, string $siteUrl, string|null $rootPath = null): string
    {
        return $siteUrl . $path . '?' . $this->getAssetCacheBuster($path, $rootPath ?? $this->sourceDir);
    }

    public function getWebpackAssetUrl(string $path, string $siteUrl): string
    {
        return $this->getAssetUrl($path, $siteUrl . '/frontend', $this->webpackBuildDir);
    }

    public function truncate(string $string, int $limit, string $separator = '...'): string
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

    private function getAssetCacheBuster(string $path, string $rootPath): string
    {
        $assetPath = realpath($rootPath . '/' . $path);
        assert(is_string($assetPath), sprintf('Failed to determine the path for the asset "%s"', $rootPath . '/' . $path));

        $contents = file_get_contents($assetPath);
        assert(is_string($contents), sprintf('Failed to load the asset located at "%s"', $rootPath . '/' . $path));

        return substr(sha1($contents), 0, 6);
    }
}
