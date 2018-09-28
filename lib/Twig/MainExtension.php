<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Doctrine\Website\Assets\AssetIntegrityGenerator;
use Doctrine\Website\Model\Project;
use Parsedown;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use function assert;
use function file_get_contents;
use function is_string;
use function realpath;
use function sha1;
use function substr;

class MainExtension extends Twig_Extension
{
    /** @var Parsedown */
    private $parsedown;

    /** @var AssetIntegrityGenerator */
    private $assetIntegrityGenerator;

    /** @var string */
    private $sourcePath;

    public function __construct(Parsedown $parsedown, AssetIntegrityGenerator $assetIntegrityGenerator, string $sourcePath)
    {
        $this->parsedown               = $parsedown;
        $this->assetIntegrityGenerator = $assetIntegrityGenerator;
        $this->sourcePath              = $sourcePath;
    }

    /**
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('get_search_box_placeholder', [$this, 'getSearchBoxPlaceholder']),
            new Twig_SimpleFunction('get_asset_url', [$this, 'getAssetUrl']),
            new Twig_SimpleFunction('get_asset_integrity', [$this->assetIntegrityGenerator, 'getAssetIntegrity']),
        ];
    }

    /**
     * @return Twig_SimpleFilter[]
     */
    public function getFilters() : array
    {
        return [
            new Twig_SimpleFilter('markdown', [$this->parsedown, 'text'], ['is_safe' => ['html']]),
        ];
    }

    public function getSearchBoxPlaceholder(?Project $project = null, ?string $version = null) : string
    {
        $projectVersion = $project !== null && $version !== null
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

    private function getAssetCacheBuster(string $path) : string
    {
        $assetPath = realpath($this->sourcePath . $path);
        assert(is_string($assetPath));

        $contents = file_get_contents($assetPath);
        assert(is_string($contents));

        return substr(sha1($contents), 0, 6);
    }
}
