<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\DataSource\DataSource;

class DoctrineUsers implements DataSource
{
    /** @var mixed[][] */
    private $doctrineUsers;

    /**
     * @param mixed[][] $doctrineUsers
     */
    public function __construct(array $doctrineUsers)
    {
        $this->doctrineUsers = $doctrineUsers;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        return $this->doctrineUsers;
    }
}
