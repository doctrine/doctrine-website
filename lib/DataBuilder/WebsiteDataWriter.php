<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

use function dirname;
use function file_put_contents;
use function is_dir;
use function json_encode;
use function mkdir;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;

final readonly class WebsiteDataWriter
{
    public function __construct(
        private string $cacheDir,
    ) {
    }

    public function write(WebsiteData $websiteData): void
    {
        $path = $this->cacheDir . '/data/' . $websiteData->getName() . '.json';
        $dir  = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents(
            $path,
            json_encode($websiteData->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        );
    }
}
