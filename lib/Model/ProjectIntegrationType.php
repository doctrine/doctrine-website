<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ProjectIntegrationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private string $name = '',
        #[ORM\Column(type: 'string')]
        private string $url = '',
        #[ORM\Column(type: 'string')]
        private string $icon = '',
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }
}
