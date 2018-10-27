<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\DataSource\Sorter;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFile;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileFilesystemReader;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileParameters;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFiles;
use Doctrine\Website\DataSources\BlogPosts;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use function usort;

class BlogPostsTest extends TestCase
{
    /** @var SourceFileFilesystemReader|MockObject */
    private $sourceFileFilesystemReader;

    /** @var BlogPosts */
    private $blogPosts;

    public function testGetSourceRows() : void
    {
        $sourceFile1 = new SourceFile(
            '/tmp/blog/test1.html',
            'test1',
            new SourceFileParameters([
                'url' => '/2018/09/02/test1.html',
                'title' => 'Test Blog Post',
                'authorName' => 'Jonathan H. Wage',
                'authorEmail' => 'jonwage@gmail.com',
                'slug' => 'test1',
            ])
        );

        $sourceFile2 = new SourceFile(
            '/tmp/blog/test2.html',
            'test2',
            new SourceFileParameters([
                'url' => '/2018/09/01/test2.html',
                'title' => 'Test Blog Post',
                'authorName' => 'Jonathan H. Wage',
                'authorEmail' => 'jonwage@gmail.com',
                'slug' => 'test2',
            ])
        );

        $sourceFiles = [$sourceFile1, $sourceFile2];

        $this->sourceFileFilesystemReader->expects(self::once())
            ->method('getSourceFiles')
            ->willReturn(new SourceFiles($sourceFiles));

        $blogPostRows = $this->blogPosts->getSourceRows();

        usort($blogPostRows, new Sorter(['date' => 'desc']));

        $expected = [
            [
                'url' => '/2018/09/02/test1.html',
                'title' => 'Test Blog Post',
                'authorName' => 'Jonathan H. Wage',
                'authorEmail' => 'jonwage@gmail.com',
                'contents' => 'test1',
                'date' => new DateTimeImmutable('2018-09-02'),
                'slug' => 'test1',
            ],
            [
                'url' => '/2018/09/01/test2.html',
                'title' => 'Test Blog Post',
                'authorName' => 'Jonathan H. Wage',
                'authorEmail' => 'jonwage@gmail.com',
                'contents' => 'test2',
                'date' => new DateTimeImmutable('2018-09-01'),
                'slug' => 'test2',
            ],

        ];

        self::assertEquals($expected, $blogPostRows);
    }

    protected function setUp() : void
    {
        $this->sourceFileFilesystemReader = $this->createMock(SourceFileFilesystemReader::class);

        $this->blogPosts = new BlogPosts(
            $this->sourceFileFilesystemReader
        );
    }
}
