<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource as SkeletonMapperDataSource;

interface DataSource extends SkeletonMapperDataSource
{
    /** @return mixed[][] */
    public function getSourceRows(): array;
}
