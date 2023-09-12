<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

use RuntimeException;

use function file_exists;
use function file_get_contents;
use function json_decode;
use function sprintf;

class WebsiteDataReader
{
    public function __construct(private string $cacheDir)
    {
    }

    public function read(string $file): WebsiteData
    {
        $jsonPath = $this->cacheDir . '/data/' . $file . '.json';

        if (! file_exists($jsonPath)) {
            throw new RuntimeException(
                sprintf(
                    'File %s does not exist. Run ./doctrine build-website-data to generate.',
                    $jsonPath,
                ),
            );
        }

        $json = file_get_contents($jsonPath);

        if ($json === false) {
            throw new RuntimeException(
                sprintf('Could not load file %s', $jsonPath),
            );
        }

        $data = json_decode($json, true);

        if ($data === null) {
            throw new RuntimeException(
                sprintf('Could not load JSON from file %s', $jsonPath),
            );
        }

        return new WebsiteData($file, $data);
    }
}
