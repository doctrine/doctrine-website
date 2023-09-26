<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileRepository;

final readonly class SitemapPages implements DataSource
{
    public function __construct(
        private SourceFileRepository $sourceFileRepository,
    ) {
    }

    /** @return mixed[][] */
    public function getSourceRows(): array
    {
        $sitemapPages = [];

        foreach ($this->sourceFileRepository->getSourceFiles() as $sourceFile) {
            $sitemapPages[] = [
                'url' => $sourceFile->getUrl(),
                'date' => new DateTimeImmutable(),
            ];
        }

        return $sitemapPages;
    }
}
