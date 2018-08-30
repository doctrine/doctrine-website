<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Builder;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileParameters;
use PHPUnit\Framework\TestCase;

class SourceFileTest extends TestCase
{
    /** @var SourceFile */
    private $sourceFile;

    public function testGetSourcePath() : void
    {
        self::assertSame('/tmp/test.md', $this->sourceFile->getSourcePath());
    }

    public function testGetWritePath() : void
    {
        self::assertSame('/tmp/test.html', $this->sourceFile->getWritePath());
    }

    public function testGetUrl() : void
    {
        self::assertSame('/2019/01/01/test.html', $this->sourceFile->getUrl());
    }

    public function testGetDate() : void
    {
        self::assertEquals('2019-01-01', $this->sourceFile->getDate()->format('Y-m-d'));
    }

    public function testGetExtension() : void
    {
        self::assertEquals('md', $this->sourceFile->getExtension());
    }

    public function testIsMarkdown() : void
    {
        $sourceFile = new SourceFile(
            'rst',
            '/tmp/test.rst',
            '/tmp/test.html',
            'test',
            new SourceFileParameters(['url' => '/2019/01/01/test.html'])
        );

        self::assertFalse($sourceFile->isMarkdown());

        self::assertTrue($this->sourceFile->isMarkdown());
    }

    public function testIsRestructuredText() : void
    {
        $sourceFile = new SourceFile(
            'rst',
            '/tmp/test.rst',
            '/tmp/test.html',
            'test',
            new SourceFileParameters(['url' => '/2019/01/01/test.html'])
        );

        self::assertTrue($sourceFile->isRestructuredText());

        self::assertFalse($this->sourceFile->isRestructuredText());
    }

    public function testIsTwig() : void
    {
        $sourceFile = new SourceFile(
            'jpg',
            '/tmp/test.jpg',
            '/tmp/test.jpg',
            'test',
            new SourceFileParameters(['url' => '/test.jpg'])
        );

        self::assertFalse($sourceFile->isTwig());

        self::assertTrue($this->sourceFile->isTwig());
    }

    public function testIsLayoutNeeded() : void
    {
        $sourceFile = new SourceFile(
            'jpg',
            '/tmp/test.jpg',
            '/tmp/test.jpg',
            'test',
            new SourceFileParameters(['url' => '/test.jpg'])
        );

        self::assertFalse($sourceFile->isLayoutNeeded());

        self::assertTrue($this->sourceFile->isLayoutNeeded());
    }

    public function isApiDocs() : void
    {
        $sourceFile = new SourceFile(
            'html',
            '/tmp/api/test.html',
            '/tmp/api/test.html',
            'test',
            new SourceFileParameters(['url' => '/api/test.html'])
        );

        self::assertTrue($sourceFile->isApiDocs());

        self::assertFalse($this->sourceFile->isApiDocs());
    }

    public function testGetContents() : void
    {
        self::assertSame('test', $this->sourceFile->getContents());
    }

    public function testGetParameters() : void
    {
        self::assertEquals(new SourceFileParameters(['url' => '/2019/01/01/test.html']), $this->sourceFile->getParameters());
    }

    public function testGetParameter() : void
    {
        self::assertSame('/2019/01/01/test.html', $this->sourceFile->getParameter('url'));
    }

    protected function setUp() : void
    {
        $this->sourceFile = new SourceFile(
            'md',
            '/tmp/test.md',
            '/tmp/test.html',
            'test',
            new SourceFileParameters(['url' => '/2019/01/01/test.html'])
        );
    }
}
