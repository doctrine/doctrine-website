<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;

class BlogPost implements LoadMetadataInterface
{
    public function __construct(
        private string $url,
        private string $slug,
        private string $title,
        private string $authorName,
        private string $authorEmail,
        private string $contents,
        private DateTimeImmutable $date,
    ) {
    }

    public static function loadMetadata(ClassMetadataInterface $metadata): void
    {
        $metadata->setIdentifier(['slug']);
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
