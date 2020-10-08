<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Git;

use Doctrine\Website\Git\TagReader;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Process\Process;

class TagReaderTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    /** @var TagReader */
    private $tagReader;

    public function testGetRepositoryTags(): void
    {
        $repositoryPath = '/test';

        $process = $this->createMock(Process::class);

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with("cd /test && git tag -l --format='refname: %(refname) creatordate: %(creatordate)'")
            ->willReturn($process);

        $output = <<<OUTPUT

this should be ignored

refname: refs/tags/v1.0.0 creatordate: 2019-01-01
refname: refs/tags/v1.0.1 creatordate: 2019-01-02
refname: refs/tags/v1.0.2 creatordate: 2019-01-03

this should be ignored

OUTPUT;

        $process->expects(self::once())
            ->method('getOutput')
            ->willReturn($output);

        $tags = $this->tagReader->getRepositoryTags(
            $repositoryPath
        );

        self::assertCount(3, $tags);

        self::assertSame('v1.0.0', $tags[0]->getName());
        self::assertSame('2019-01-01', $tags[0]->getDate()->format('Y-m-d'));

        self::assertSame('v1.0.1', $tags[1]->getName());
        self::assertSame('2019-01-02', $tags[1]->getDate()->format('Y-m-d'));

        self::assertSame('v1.0.2', $tags[2]->getName());
        self::assertSame('2019-01-03', $tags[2]->getDate()->format('Y-m-d'));
    }

    protected function setUp(): void
    {
        $this->processFactory = $this->createMock(ProcessFactory::class);
        $this->tagReader      = new TagReader(
            $this->processFactory
        );
    }
}
