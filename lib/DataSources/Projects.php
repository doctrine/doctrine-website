<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\DataSource\DataSource;
use Doctrine\Website\Projects\ProjectDataReader;
use function array_replace;

class Projects implements DataSource
{
    private const DEFAULTS = [
        'active'        => true,
        'archived'      => false,
        'hasDocs'       => true,
        'integration'   => false,
    ];

    /** @var ProjectDataReader */
    private $projectDataReader;

    /** @var mixed[][] */
    private $projectsData = [];

    /**
     * @param mixed[][] $projectsData
     */
    public function __construct(ProjectDataReader $projectDataReader, array $projectsData)
    {
        $this->projectDataReader = $projectDataReader;
        $this->projectsData      = $projectsData;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        $projectRows = [];

        foreach ($this->projectsData as $projectData) {
            $projectRows[] = array_replace(
                self::DEFAULTS,
                $this->projectDataReader->read($projectData['repositoryName'])
            );
        }

        return $projectRows;
    }
}
