<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Blog;

use Doctrine\Website\Blog\BlogPostRepository;
use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileParameters;
use Doctrine\Website\Builder\SourceFileRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BlogPostRepositoryTest extends TestCase
{
    /** @var SourceFileRepository|MockObject */
    private $sourceFileRepository;

    /** @var BlogPostRepository */
    private $blogPostRepository;

    public function testFind() : void
    {
        $sourceFile = new SourceFile('md', '/tmp/test.html', 'test', new SourceFileParameters([
            'url' => '/2018/09/01/test.html',
            'title' => 'Test Blog Post',
            'authorName' => 'Jonathan H. Wage',
            'authorEmail' => 'jonwage@gmail.com',
        ]));

        $blogPost = $this->blogPostRepository->find(
            $sourceFile
        );

        self::assertSame('/2018/09/01/test.html', $blogPost->getUrl());
        self::assertSame('Test Blog Post', $blogPost->getTitle());
        self::assertSame('Jonathan H. Wage', $blogPost->getAuthorName());
        self::assertSame('jonwage@gmail.com', $blogPost->getAuthorEmail());
        self::assertSame('test', $blogPost->getContents());
        self::assertSame('2018-09-01', $blogPost->getDate()->format('Y-m-d'));
    }

    public function testFindAll() : void
    {
        $sourceFile1 = new SourceFile('md', '/tmp/test1.html', 'test1', new SourceFileParameters([
            'url' => '/2018/09/02/test1.html',
            'title' => 'Test Blog Post',
            'authorName' => 'Jonathan H. Wage',
            'authorEmail' => 'jonwage@gmail.com',
        ]));

        $sourceFile2 = new SourceFile('md', '/tmp/test.html', 'test2', new SourceFileParameters([
            'url' => '/2018/09/01/test2.html',
            'title' => 'Test Blog Post',
            'authorName' => 'Jonathan H. Wage',
            'authorEmail' => 'jonwage@gmail.com',
        ]));

        $files = [$sourceFile1, $sourceFile2];

        $this->sourceFileRepository->expects(self::once())
            ->method('getFiles')
            ->with('', 'source/blog')
            ->willReturn($files);

        $blogPosts = $this->blogPostRepository->findAll();

        self::assertCount(2, $blogPosts);

        self::assertSame('/2018/09/02/test1.html', $blogPosts[0]->getUrl());
        self::assertSame('Test Blog Post', $blogPosts[0]->getTitle());
        self::assertSame('Jonathan H. Wage', $blogPosts[0]->getAuthorName());
        self::assertSame('jonwage@gmail.com', $blogPosts[0]->getAuthorEmail());
        self::assertSame('test1', $blogPosts[0]->getContents());
        self::assertSame('2018-09-02', $blogPosts[0]->getDate()->format('Y-m-d'));

        self::assertSame('/2018/09/01/test2.html', $blogPosts[1]->getUrl());
        self::assertSame('Test Blog Post', $blogPosts[1]->getTitle());
        self::assertSame('Jonathan H. Wage', $blogPosts[1]->getAuthorName());
        self::assertSame('jonwage@gmail.com', $blogPosts[1]->getAuthorEmail());
        self::assertSame('test2', $blogPosts[1]->getContents());
        self::assertSame('2018-09-01', $blogPosts[1]->getDate()->format('Y-m-d'));
    }

    protected function setUp() : void
    {
        $this->sourceFileRepository = $this->createMock(SourceFileRepository::class);

        $this->blogPostRepository = new BlogPostRepository(
            $this->sourceFileRepository
        );
    }
}
