<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileRepository;
use Doctrine\Website\Twig\BlogExtension;
use PHPUnit\Framework\TestCase;

class BlogExtensionTest extends TestCase
{
    /** @var SourceFileRepository */
    private $sourceFileRepository;

    /** @var BlogExtension */
    private $blogExtension;

    protected function setUp() : void
    {
        $this->sourceFileRepository = $this->createMock(SourceFileRepository::class);

        $this->blogExtension = new BlogExtension(
            $this->sourceFileRepository
        );
    }

    public function testGetBlogPosts() : void
    {
        $file1 = $this->createMock(SourceFile::class);
        $file2 = $this->createMock(SourceFile::class);

        $this->sourceFileRepository->expects(self::once())
            ->method('getFiles')
            ->with('', 'source/blog')
            ->willReturn([$file1, $file2]);

        $blogPosts = $this->blogExtension->getBlogPosts();

        self::assertSame([$file2, $file1], $blogPosts);
    }
}
