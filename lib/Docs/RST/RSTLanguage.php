<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Website\Model\ProjectVersion;

#[ORM\Entity]
final class RSTLanguage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: ProjectVersion::class, inversedBy: 'docsLanguages')]
    private ProjectVersion $projectVersion;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $code,
        #[ORM\Column(type: 'string')]
        private readonly string $path,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setProjectVersion(ProjectVersion $version): void
    {
        $this->projectVersion = $version;
    }

    public function getProjectVersion(): ProjectVersion
    {
        return $this->projectVersion;
    }
}
