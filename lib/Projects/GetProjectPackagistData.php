<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use function file_get_contents;
use function json_decode;
use function sprintf;

/** @final */
class GetProjectPackagistData
{
    public function __construct(
        private readonly string $packagistUrlFormat,
    ) {
    }

    /** @return mixed[] */
    public function __invoke(string $composerPackageName): array
    {
        $packagistUrl = sprintf($this->packagistUrlFormat, $composerPackageName);

        $response = file_get_contents($packagistUrl);

        $projectPackagistData = $response !== false ? json_decode($response, true) : [];

        return $projectPackagistData ?? [];
    }
}
