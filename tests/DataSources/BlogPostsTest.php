<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use DateTimeImmutable;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFile;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileFilesystemReader;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileParameters;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFiles;
use Doctrine\Website\DataSources\BlogPosts;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BlogPostsTest extends TestCase
{
    private SourceFileFilesystemReader&MockObject $sourceFileFilesystemReader;

    private BlogPosts $blogPosts;

    public function testGetSourceRows(): void
    {
        $parameters  = new SourceFileParameters([
            'url' => 'blog/2161/01/01/new-doctrine.rst',
            'slug' => 'new-doctrine.rst',
            'title' => 'New Doctrine',
            'authorName' => 'John Doe',
            'authorEmail' => 'john.doe@doctrine-website.org',
        ]);
        $sourceFile  = new SourceFile(__DIR__ . '/blog/', 'Content, the final frontier', $parameters);
        $sourceFiles = new SourceFiles([$sourceFile]);

        $this->sourceFileFilesystemReader->expects(self::once())
            ->method('getSourceFiles')
            ->with('')
            ->willReturn($sourceFiles);

        $blogPostRows = $this->blogPosts->getSourceRows();

        $expected = [
            [
                'url' => 'blog/2161/01/01/new-doctrine.rst',
                'slug' => 'new-doctrine.rst',
                'title' => 'New Doctrine',
                'authorName' => 'John Doe',
                'authorEmail' => 'john.doe@doctrine-website.org',
                'contents' => 'Content, the final frontier',
                'date' => new DateTimeImmutable('2161-01-01'),
            ],
        ];

        self::assertEquals($expected, $blogPostRows);
    }

    protected function setUp(): void
    {
        $this->sourceFileFilesystemReader = $this->createMock(SourceFileFilesystemReader::class);

        $this->blogPosts = new BlogPosts(
            $this->sourceFileFilesystemReader,
        );
    }
}
