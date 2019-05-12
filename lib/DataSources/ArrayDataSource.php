<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;

final class ArrayDataSource implements DataSource
{
    /** @var mixed[] */
    private $sourceRows;

    /**
     * @param mixed[] $sourceRows
     */
    public function __construct(array $sourceRows)
    {
        $this->sourceRows = $sourceRows;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        return $this->sourceRows;
    }
}
