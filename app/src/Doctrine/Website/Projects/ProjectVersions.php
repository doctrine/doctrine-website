<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Countable;
use Iterator;
use function count;
use function current;
use function key;
use function next;
use function reset;

class ProjectVersions implements Iterator, Countable
{
    /** @var array */
    private $versions;

    public function __construct(array $versions)
    {
        foreach ($versions as $version) {
            $this->versions[] = $version instanceof ProjectVersion
                ? $version
                : new ProjectVersion($version)
            ;
        }
    }

    public function count()
    {
        return count($this->versions);
    }

    public function rewind()
    {
        return reset($this->versions);
    }

    public function current()
    {
        return current($this->versions);
    }

    public function key()
    {
        return key($this->versions);
    }

    public function next()
    {
        return next($this->versions);
    }

    public function valid()
    {
        return key($this->versions) !== null;
    }
}
