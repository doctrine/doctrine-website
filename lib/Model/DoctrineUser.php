<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Website\Repositories\DoctrineUserRepository;

#[ORM\Entity(repositoryClass: DoctrineUserRepository::class)]
readonly class DoctrineUser
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        private string $name,
        #[ORM\Column(type: 'string')]
        private string $url,
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
}
