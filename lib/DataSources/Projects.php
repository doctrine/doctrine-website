<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\Website\DataBuilder\ProjectDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataReader;

class Projects implements DataSource
{
    /** @var WebsiteDataReader */
    private $dataReader;

    public function __construct(WebsiteDataReader $dataReader)
    {
        $this->dataReader = $dataReader;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        return $this->dataReader
            ->read(ProjectDataBuilder::DATA_FILE)
            ->getData();
    }
}
