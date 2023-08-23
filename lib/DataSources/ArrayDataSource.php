<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;

final class ArrayDataSource implements DataSource
{
    /** @param mixed[] $sourceRows */
    public function __construct(private array $sourceRows)
    {
    }

    /** @return mixed[][] */
    public function getSourceRows(): array
    {
        return $this->sourceRows;
    }
}
