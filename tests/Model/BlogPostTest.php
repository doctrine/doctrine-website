<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Model;

use DateTimeImmutable;
use Doctrine\Website\Model\BlogPost;
use Doctrine\Website\Tests\TestCase;

class BlogPostTest extends TestCase
{
    private string $url;

    private string $slug;

    private string $title;

    private string $authorName;

    private string $authorEmail;

    private string $contents;

    private DateTimeImmutable $date;

    private BlogPost $blogPost;

    public function testGetUrl(): void
    {
        self::assertSame($this->url, $this->blogPost->getUrl());
    }

    public function testGetSlug(): void
    {
        self::assertSame($this->slug, $this->blogPost->getSlug());
    }

    public function testGetTitle(): void
    {
        self::assertSame($this->title, $this->blogPost->getTitle());
    }

    public function testGetAuthorName(): void
    {
        self::assertSame($this->authorName, $this->blogPost->getAuthorName());
    }

    public function testGetAuthorEmail(): void
    {
        self::assertSame($this->authorEmail, $this->blogPost->getAuthorEmail());
    }

    public function testGetContents(): void
    {
        self::assertSame($this->contents, $this->blogPost->getContents());
    }

    public function testGetDate(): void
    {
        self::assertSame($this->date, $this->blogPost->getDate());
    }

    protected function setUp(): void
    {
        $this->url         = 'http://lcl.doctrine-project.org';
        $this->slug        = 'test-blog-post';
        $this->title       = 'Test Blog Post';
        $this->authorName  = 'Jonathan H. Wage';
        $this->authorEmail = 'jonwage@gmail.com';
        $this->contents    = 'Test Content';
        $this->date        = new DateTimeImmutable();

        $this->blogPost = new BlogPost(
            $this->url,
            $this->slug,
            $this->title,
            $this->authorName,
            $this->authorEmail,
            $this->contents,
            $this->date,
        );
    }
}
