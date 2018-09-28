<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\DataSource\DataSource;

class TeamMembers implements DataSource
{
    /** @var mixed[] */
    private $teamMembers;

    /**
     * @param mixed[] $teamMembers
     */
    public function __construct(array $teamMembers)
    {
        $this->teamMembers = $teamMembers;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        return $this->teamMembers;
    }
}
