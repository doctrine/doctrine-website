<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\DataSource\DataSource;
use function array_replace;
use function array_values;
use function ksort;

class TeamMembers implements DataSource
{
    private const DEFAULTS = [
        'active'        => false,
        'core'          => false,
        'documentation' => false,
    ];

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
    public function getData() : array
    {
        $teamMembers = [];

        foreach ($this->teamMembers as $key => $teamMember) {
            $name = $teamMember['name'] ?? $key;

            $teamMembers[$name] = array_replace(self::DEFAULTS, $teamMember);
        }

        ksort($teamMembers);

        return array_values($teamMembers);
    }
}
