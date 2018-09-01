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
    private $sourcePath;

    /** @var string[] */
    private $cache = [];

    public function __construct(string $sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    public function getAssetIntegrity(string $path) : string
    {
        if (! isset($this->cache[$path])) {
            $contents = $this->getFileContents($path);

            $this->cache[$path] = $this->buildAssetIntegrityString($contents);
        }

        return $this->cache[$path];
    }

    private function getFileContents(string $path) : string
    {
        $assetPath = realpath($this->sourcePath . '/' . $path);
        assert($assetPath !== false);

        $contents = file_get_contents($assetPath);
        assert($contents !== false);

        return $contents;
    }

    private function buildAssetIntegrityString(string $contents) : string
    {
        return 'sha384-' . base64_encode(hash('sha384', $contents, true));
    }
}
