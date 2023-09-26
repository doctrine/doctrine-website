<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\Website\DataBuilder\ProjectDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataReader;

final readonly class Projects implements DataSource
{
    public function __construct(
        private WebsiteDataReader $dataReader,
    ) {
    }

    /** @return mixed[][] */
    public function getSourceRows(): array
    {
        return $this->dataReader
            ->read(ProjectDataBuilder::DATA_FILE)
            ->getData();
    }
}
