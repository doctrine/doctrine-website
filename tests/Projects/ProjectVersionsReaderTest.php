<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use DateTimeImmutable;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Git\TagBranchGuesser;
use Doctrine\Website\Git\TagReader;
use Doctrine\Website\Projects\ProjectVersionsReader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProjectVersionsReaderTest extends TestCase
{
    /** @var TagReader|MockObject */
    private $tagReader;

    /** @var TagBranchGuesser|MockObject */
    private $tagBranchGuesser;

    /** @var ProjectVersionsReader */
    private $projectVersionsReader;


    public function testReadProjectVersions() : void
    {
        $repositoryPath = '/repository/path';

        $tag1 = new Tag('v2.0.0', new DateTimeImmutable());
        $tag2 = new Tag('v2.0.1', new DateTimeImmutable());
        $tag3 = new Tag('v3.0.0', new DateTimeImmutable());
        $tag4 = new Tag('dev-test', new DateTimeImmutable());

        $tags = [$tag1, $tag2, $tag3, $tag4];

        $this->tagReader->expects(self::once())
            ->method('getRepositoryTags')
            ->willReturn($repositoryPath)
            ->willReturn($tags);

        // tag1
        $this->tagBranchGuesser->expects(self::at(0))
            ->method('generateTagBranchSlug')
            ->with($tag1)
            ->willReturn('2.0');

        $this->tagBranchGuesser->expects(self::at(1))
            ->method('guessTagBranchName')
            ->with($repositoryPath, $tag1)
            ->willReturn('2.0');

        // tag2
        $this->tagBranchGuesser->expects(self::at(2))
            ->method('generateTagBranchSlug')
            ->with($tag2)
            ->willReturn('2.0');

        $this->tagBranchGuesser->expects(self::at(3))
            ->method('guessTagBranchName')
            ->with($repositoryPath, $tag2)
            ->willReturn('2.0');

        // tag3
        $this->tagBranchGuesser->expects(self::at(4))
            ->method('generateTagBranchSlug')
            ->with($tag3)
            ->willReturn('3.0');

        $this->tagBranchGuesser->expects(self::at(5))
            ->method('guessTagBranchName')
            ->with($repositoryPath, $tag3)
            ->willReturn('3.0');

        $versions = $this->projectVersionsReader->readProjectVersions(
            $repositoryPath
        );

        self::assertCount(2, $versions);

        self::assertCount(2, $versions[0]['tags']);
        self::assertSame('v2.0.0', $versions[0]['tags'][0]->getName());
        self::assertSame('v2.0.1', $versions[0]['tags'][1]->getName());

        self::assertCount(1, $versions[1]['tags']);
        self::assertSame('v3.0.0', $versions[1]['tags'][0]->getName());
    }

    protected function setUp() : void
    {
        $this->tagReader        = $this->createMock(TagReader::class);
        $this->tagBranchGuesser = $this->createMock(TagBranchGuesser::class);

        $this->projectVersionsReader = new ProjectVersionsReader(
            $this->tagReader,
            $this->tagBranchGuesser
        );
    }
}
