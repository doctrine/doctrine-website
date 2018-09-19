<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use DateTimeImmutable;
use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileParameters;
use Doctrine\Website\Builder\SourceFileRepository;
use Doctrine\Website\DataSources\BlogPosts;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BlogPostsTest extends TestCase
{
    /** @var SourceFileRepository|MockObject */
    private $sourceFileRepository;

    /** @var BlogPosts */
    private $blogPosts;

    public function testGetBlogPostsData() : void
    {
        $sourceFile1 = new SourceFile(
            'md',
            '/tmp/test1.html',
            '/tmp/test1.html',
            'test1',
            new SourceFileParameters([
                'url' => '/2018/09/02/test1.html',
                'title' => 'Test Blog Post',
                'authorName' => 'Jonathan H. Wage',
                'authorEmail' => 'jonwage@gmail.com',
            ])
        );

        $sourceFile2 = new SourceFile(
            'md',
            '/tmp/test.html',
            '/tmp/test.html',
            'test2',
            new SourceFileParameters([
                'url' => '/2018/09/01/test2.html',
                'title' => 'Test Blog Post',
                'authorName' => 'Jonathan H. Wage',
                'authorEmail' => 'jonwage@gmail.com',
            ])
        );

        $files = [$sourceFile1, $sourceFile2];

        $this->sourceFileRepository->expects(self::once())
            ->method('getFiles')
            ->with('', 'source/blog')
            ->willReturn($files);

        $blogPostsData = $this->blogPosts->getData();

        $expected = [
            [
                'url' => '/2018/09/02/test1.html',
                'title' => 'Test Blog Post',
                'authorName' => 'Jonathan H. Wage',
                'authorEmail' => 'jonwage@gmail.com',
                'contents' => 'test1',
                'date' => new DateTimeImmutable('2018-09-02'),
            ],
            [
                'url' => '/2018/09/01/test2.html',
                'title' => 'Test Blog Post',
                'authorName' => 'Jonathan H. Wage',
                'authorEmail' => 'jonwage@gmail.com',
                'contents' => 'test2',
                'date' => new DateTimeImmutable('2018-09-01'),
            ],

        ];

        self::assertEquals($expected, $blogPostsData);
    }

    protected function setUp() : void
    {
        $this->sourceFileRepository = $this->createMock(SourceFileRepository::class);

        $this->blogPosts = new BlogPosts(
            $this->sourceFileRepository
        );
    }
}
