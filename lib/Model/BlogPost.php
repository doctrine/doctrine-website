<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\Hydrator\HydratableInterface;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;

class BlogPost implements HydratableInterface, LoadMetadataInterface
{
    /** @var string */
    private $url;

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
        string $title,
        string $authorName,
        string $authorEmail,
        string $contents,
        DateTimeImmutable $date
    ) {
        $this->url         = $url;
        $this->title       = $title;
        $this->authorName  = $authorName;
        $this->authorEmail = $authorEmail;
        $this->contents    = $contents;
        $this->date        = $date;
    }

    public static function loadMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->setIdentifier(['url']);
    }

    /**
     * @param mixed[] $project
     */
    public function hydrate(array $project, ObjectManagerInterface $objectManager) : void
    {
        $this->url         = (string) $project['url'] ?? '';
        $this->title       = (string) $project['title'] ?? '';
        $this->authorName  = (string) $project['authorName'] ?? '';
        $this->authorEmail = (string) $project['authorEmail'] ?? '';
        $this->contents    = (string) $project['contents'] ?? '';
        $this->date        = $project['date'] ?? new DateTimeImmutable();
    }

    public function getUrl() : string
    {
        return $this->url;
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
