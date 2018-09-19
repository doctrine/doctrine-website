<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

class ProjectIntegrationType
{
    /** @var string */
    private $name;

    /** @var string */
    private $url;

    /** @var string */
    private $icon;

    /**
     * @param mixed[] $projectIntegrationType
     */
    public function __construct(array $projectIntegrationType)
    {
        $this->name = (string) ($projectIntegrationType['name'] ?? '');
        $this->url  = (string) ($projectIntegrationType['url'] ?? '');
        $this->icon = (string) ($projectIntegrationType['icon'] ?? '');
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
