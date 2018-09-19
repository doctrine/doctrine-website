<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use function array_merge;

class ProjectVersion
{
    /** @var string */
    private $name;

    /** @var string */
    private $branchName;

    /** @var string */
    private $slug;

    /** @var bool */
    private $current = false;

    /** @var bool */
    private $maintained = true;

    /** @var bool */
    private $upcoming = false;

    /** @var string[] */
    private $aliases;

    /**
     * @param mixed[] $version
     */
    public function __construct(array $version)
    {
        $this->name       = (string) ($version['name'] ?? '');
        $this->branchName = (string) ($version['branchName'] ?? '');
        $this->slug       = (string) ($version['slug'] ?? '');
        $this->current    = (bool) ($version['current'] ?? false);
        $this->maintained = (bool) ($version['maintained'] ?? true);
        $this->upcoming   = (bool) ($version['upcoming'] ?? false);
        $this->aliases    = $version['aliases'] ?? [];

        if (! $this->current) {
            return;
        }

        $this->aliases = array_merge($this->aliases, ['current', 'stable']);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getBranchName() : string
    {
        return $this->branchName;
    }

    public function getSlug() : string
    {
        return $this->slug;
    }

    public function isCurrent() : bool
    {
        return $this->current;
    }

    public function isMaintained() : bool
    {
        return $this->maintained;
    }

    public function isUpcoming() : bool
    {
        return $this->upcoming;
    }

    /**
     * @return string[]
     */
    public function getAliases() : array
    {
        return $this->aliases;
    }
}
