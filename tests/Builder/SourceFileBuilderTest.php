<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Builder;

use Doctrine\RST\Document;
use Doctrine\RST\Parser as RSTParser;
use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileBuilder;
use Doctrine\Website\Builder\SourceFileRenderer;
use Parsedown;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class SourceFileBuilderTest extends TestCase
{
    /** @var SourceFileRenderer|MockObject */
    private $sourceFileRenderer;

    /** @var Filesystem|MockObject */
    private $filesystem;

    /** @var Parsedown|MockObject */
    private $parsedown;

    /** @var RSTParser|MockObject */
    private $rstParser;

    /** @var SourceFileBuilder */
    private $sourceFileBuilder;

    public function testBuildFileMarkdown() : void
    {
        $sourceFile = $this->createMock(SourceFile::class);

        $sourceFile->expects(self::once())
            ->method('getContents')
            ->willReturn('test markdown');

        $sourceFile->expects(self::once())
            ->method('isMarkdown')
            ->willReturn(true);

        $this->parsedown->expects(self::once())
            ->method('text')
            ->with('test markdown')
            ->willReturn('test markdown rendered');

        $sourceFile->expects(self::once())
            ->method('isTwig')
            ->willReturn(true);

        $this->sourceFileRenderer->expects(self::once())
            ->method('render')
            ->with($sourceFile, 'test markdown rendered')
            ->willReturn('test markdown rendered twig');

        $sourceFile->expects(self::once())
            ->method('getWritePath')
            ->willReturn('/tmp/test.html');

        $this->filesystem->expects(self::once())
            ->method('dumpFile')
            ->with('/tmp/test.html', 'test markdown rendered twig');

        $this->sourceFileBuilder->buildFile($sourceFile, '/tmp');
    }

    public function testBuildFileRestructuredText() : void
    {
        $sourceFile = $this->createMock(SourceFile::class);

        $sourceFile->expects(self::once())
            ->method('getContents')
            ->willReturn('test restructured text');

        $sourceFile->expects(self::once())
            ->method('isMarkdown')
            ->willReturn(false);

        $sourceFile->expects(self::once())
            ->method('isRestructuredText')
            ->willReturn(true);

        $document = $this->createMock(Document::class);

        $this->rstParser->expects(self::once())
            ->method('parse')
            ->with('test restructured text')
            ->willReturn($document);

        $document->expects(self::once())
            ->method('render')
            ->willReturn('test restructured text rendered');

        $sourceFile->expects(self::once())
            ->method('isTwig')
            ->willReturn(true);

        $this->sourceFileRenderer->expects(self::once())
            ->method('render')
            ->with($sourceFile, 'test restructured text rendered')
            ->willReturn('test restructured text rendered twig');

        $sourceFile->expects(self::once())
            ->method('getWritePath')
            ->willReturn('/tmp/test.html');

        $this->filesystem->expects(self::once())
            ->method('dumpFile')
            ->with('/tmp/test.html', 'test restructured text rendered twig');

        $this->sourceFileBuilder->buildFile($sourceFile, '/tmp');
    }

    public function testBuildFileNoTwig() : void
    {
        $sourceFile = $this->createMock(SourceFile::class);

        $sourceFile->expects(self::once())
            ->method('getContents')
            ->willReturn('test restructured text');

        $sourceFile->expects(self::once())
            ->method('isMarkdown')
            ->willReturn(false);

        $sourceFile->expects(self::once())
            ->method('isRestructuredText')
            ->willReturn(true);

        $document = $this->createMock(Document::class);

        $this->rstParser->expects(self::once())
            ->method('parse')
            ->with('test restructured text')
            ->willReturn($document);

        $document->expects(self::once())
            ->method('render')
            ->willReturn('test restructured text rendered');

        $sourceFile->expects(self::once())
            ->method('isTwig')
            ->willReturn(false);

        $this->sourceFileRenderer->expects(self::never())
            ->method('render');

        $sourceFile->expects(self::once())
            ->method('getWritePath')
            ->willReturn('/tmp/test.html');

        $this->filesystem->expects(self::once())
            ->method('dumpFile')
            ->with('/tmp/test.html', 'test restructured text rendered');

        $this->sourceFileBuilder->buildFile($sourceFile, '/tmp');
    }

    protected function setUp() : void
    {
        $this->sourceFileRenderer = $this->createMock(SourceFileRenderer::class);
        $this->filesystem         = $this->createMock(Filesystem::class);
        $this->parsedown          = $this->createMock(Parsedown::class);
        $this->rstParser          = $this->createMock(RSTParser::class);

        $this->sourceFileBuilder = new SourceFileBuilder(
            $this->sourceFileRenderer,
            $this->filesystem,
            $this->parsedown,
            $this->rstParser
        );
    }
}
