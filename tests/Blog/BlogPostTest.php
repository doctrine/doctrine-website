<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Blog;

use DateTimeImmutable;
use Doctrine\Website\Blog\BlogPost;
use Doctrine\Website\Tests\TestCase;

class BlogPostTest extends TestCase
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

    /** @var BlogPost */
    private $blogPost;

    public function testGetUrl() : void
    {
        self::assertSame($this->url, $this->blogPost->getUrl());
    }

    public function testGetTitle() : void
    {
        self::assertSame($this->title, $this->blogPost->getTitle());
    }

    public function testGetAuthorName() : void
    {
        self::assertSame($this->authorName, $this->blogPost->getAuthorName());
    }

    public function testGetAuthorEmail() : void
    {
        self::assertSame($this->authorEmail, $this->blogPost->getAuthorEmail());
    }

    public function testGetContents() : void
    {
        self::assertSame($this->contents, $this->blogPost->getContents());
    }

    public function testGetDate() : void
    {
        self::assertSame($this->date, $this->blogPost->getDate());
    }

    protected function setUp() : void
    {
        $this->url         = 'http://lcl.doctrine-project.org';
        $this->title       = 'Test Blog Post';
        $this->authorName  = 'Jonathan H. Wage';
        $this->authorEmail = 'jonwage@gmail.com';
        $this->contents    = 'Test Content';
        $this->date        = new DateTimeImmutable();

        $this->blogPost = new BlogPost(
            $this->url,
            $this->title,
            $this->authorName,
            $this->authorEmail,
            $this->contents,
            $this->date
        );
    }
}
