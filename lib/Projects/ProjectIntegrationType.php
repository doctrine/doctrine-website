<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

class ProjectIntegrationType
{
    /** @var string */
    private $name;

    /** @var string */
    private $url;

    /** @var string */
    private $icon;

    /**
     * @param string[] $projectIntegrationType
     */
    public function __construct(array $projectIntegrationType)
    {
        $this->name = $projectIntegrationType['name'] ?? '';
        $this->url  = $projectIntegrationType['url'] ?? '';
        $this->icon = $projectIntegrationType['icon'] ?? '';
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getIcon() : string
    {
        return $this->icon;
    }
}
