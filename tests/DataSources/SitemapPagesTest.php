<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataSources\SitemapPages;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFile;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileParameters;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFileRepository;
use Doctrine\Website\StaticGenerator\SourceFile\SourceFiles;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function date;

class SitemapPagesTest extends TestCase
{
    private SourceFileRepository&MockObject $sourceFileRepository;

    private SitemapPages $sitemapPages;

    public function testGetSourceRows(): void
    {
        $this->sourceFileRepository->expects(self::once())
            ->method('getSourceFiles')
            ->willReturn(new SourceFiles([
                new SourceFile('/index.html', '', new SourceFileParameters(['url' => '/'])),
            ]));

        $sitemapPageRows = $this->sitemapPages->getSourceRows();

        self::assertCount(1, $sitemapPageRows);

        self::assertSame(date('Y-m-d'), $sitemapPageRows[0]['date']->format('Y-m-d'));

        self::assertSame('/', $sitemapPageRows[0]['url']);
    }

    protected function setUp(): void
    {
        $this->sourceFileRepository = $this->createMock(SourceFileRepository::class);

        $this->sitemapPages = new SitemapPages($this->sourceFileRepository);
    }
}
