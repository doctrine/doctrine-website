<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Git;

use DateTimeImmutable;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Git\TagBranchGuesser;
use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Process\Process;

class TagBranchGuesserTest extends TestCase
{
    /** @var ProcessFactory|MockObject */
    private $processFactory;

    /** @var TagBranchGuesser */
    private $tagBranchGuesser;

    public function testGuessTagBranchNameGuess1(): void
    {
        $tag = new Tag('v2.0.0-alpha1', new DateTimeImmutable());

        $repositoryPath = '/repo/path';

        $process = $this->createMock(Process::class);

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with('cd /repo/path && git branch -a')
            ->willReturn($process);

        $process->expects(self::once())
            ->method('getOutput')
            ->willReturn('remotes/origin/2.0');

        $branchName = $this->tagBranchGuesser->guessTagBranchName(
            $repositoryPath,
            $tag,
        );

        self::assertSame('2.0', $branchName);
    }

    public function testGuessTagBranchNameGuess2(): void
    {
        $tag = new Tag('v2.0.0-alpha1', new DateTimeImmutable());

        $repositoryPath = '/repo/path';

        $process = $this->createMock(Process::class);

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with('cd /repo/path && git branch -a')
            ->willReturn($process);

        $process->expects(self::once())
            ->method('getOutput')
            ->willReturn('remotes/origin/2.0.x');

        $branchName = $this->tagBranchGuesser->guessTagBranchName(
            $repositoryPath,
            $tag,
        );

        self::assertSame('2.0.x', $branchName);
    }

    public function testGuessTagBranchNameReturnsNull(): void
    {
        $tag = new Tag('v2.0.0-alpha1', new DateTimeImmutable());

        $repositoryPath = '/repo/path';

        $process = $this->createMock(Process::class);

        $this->processFactory->expects(self::once())
            ->method('run')
            ->with('cd /repo/path && git branch -a')
            ->willReturn($process);

        $process->expects(self::once())
            ->method('getOutput')
            ->willReturn('');

        $branchName = $this->tagBranchGuesser->guessTagBranchName(
            $repositoryPath,
            $tag,
        );

        self::assertNull($branchName);
    }

    public function testGenerateTagBranchSlug(): void
    {
        $tag = new Tag('v2.0.0-alpha1', new DateTimeImmutable());

        self::assertSame('2.0', $this->tagBranchGuesser->generateTagBranchSlug($tag));
    }

    protected function setUp(): void
    {
        $this->processFactory = $this->createMock(ProcessFactory::class);

        $this->tagBranchGuesser = new TagBranchGuesser(
            $this->processFactory,
        );
    }
}
