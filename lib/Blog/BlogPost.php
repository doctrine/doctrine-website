<?php

declare(strict_types=1);

namespace Doctrine\Website\Blog;

use DateTimeImmutable;

class BlogPost
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
