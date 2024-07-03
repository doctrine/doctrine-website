<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Website\Repositories\BlogPostRepository;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
class BlogPost
{
    public function __construct(
        #[ORM\Column(type: 'string')]
        private string $url,
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        private string $slug,
        #[ORM\Column(type: 'string')]
        private string $title,
        #[ORM\Column(type: 'string')]
        private string $authorName,
        #[ORM\Column(type: 'string')]
        private string $authorEmail,
        #[ORM\Column(type: 'text')]
        private string $contents,
        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $date,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
