<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;

class Sponsors implements DataSource
{
    /** @var mixed[][] */
    private $sponsors;

    /**
     * @param mixed[][] $sponsors
     */
    public function __construct(array $sponsors)
    {
        $this->sponsors = $sponsors;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        return $this->sponsors;
    }
}
