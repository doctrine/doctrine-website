<?php

declare(strict_types=1);

namespace Doctrine\Website\Assets;

use function assert;
use function base64_encode;
use function file_get_contents;
use function hash;
use function realpath;

class AssetIntegrityGenerator
{
    /** @var string */
    private $sourceDir;

    /** @var string */
    private $webpackBuildDir;

    /** @var string[] */
    private $cache = [];

    public function __construct(string $sourceDir, string $webpackBuildDir)
    {
        $this->sourceDir       = $sourceDir;
        $this->webpackBuildDir = $webpackBuildDir;
    }

    public function getAssetIntegrity(string $path, ?string $rootPath = null): string
    {
        if (! isset($this->cache[$path])) {
            $contents = $this->getFileContents($path, $rootPath ?? $this->sourceDir);

            $this->cache[$path] = $this->buildAssetIntegrityString($contents);
        }

        return $this->cache[$path];
    }

    public function getWebpackAssetIntegrity(string $path): string
    {
        return $this->getAssetIntegrity($path, $this->webpackBuildDir);
    }

    private function getFileContents(string $path, string $rootPath): string
    {
        $assetPath = realpath($rootPath . '/' . $path);
        assert($assetPath !== false);

        $contents = file_get_contents($assetPath);
        assert($contents !== false);

        return $contents;
    }

    private function buildAssetIntegrityString(string $contents): string
    {
        return 'sha384-' . base64_encode(hash('sha384', $contents, true));
    }
}
