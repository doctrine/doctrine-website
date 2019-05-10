<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;

class Partners implements DataSource
{
    /** @var mixed[][] */
    private $partners;

    /**
     * @param mixed[][] $partners
     */
    public function __construct(array $partners)
    {
        $this->partners = $partners;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        return $this->partners;
    }
}
