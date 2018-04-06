<?php

namespace Doctrine\Website\Projects;

use Iterator;

class ProjectVersions implements Iterator
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
