<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;

class BlogPost implements LoadMetadataInterface
{
    /** @var string */
    private $url;

    /** @var string */
    private $slug;

    /** @var string */
    private $title;

    /** @var string */
    private $authorName;

    /** @var string */
    private $authorEmail;

    /** @var string */
    private $contents;

    /** @var DateTimeImmutable */
    private $date;

    public function __construct(
        string $url,
        string $slug,
        string $title,
        string $authorName,
        string $authorEmail,
        string $contents,
        DateTimeImmutable $date
    ) {
        $this->url         = $url;
        $this->slug        = $slug;
        $this->title       = $title;
        $this->authorName  = $authorName;
        $this->authorEmail = $authorEmail;
        $this->contents    = $contents;
        $this->date        = $date;
    }

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['slug']);
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function getSlug() : string
    {
        return $this->slug;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getAuthorName() : string
    {
        return $this->authorName;
    }

    public function getAuthorEmail() : string
    {
        return $this->authorEmail;
    }

    public function getContents() : string
    {
        return $this->contents;
    }

    public function getDate() : DateTimeImmutable
    {
        return $this->date;
    }
}
