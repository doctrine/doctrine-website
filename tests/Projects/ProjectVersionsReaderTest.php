<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use DateTimeImmutable;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Git\TagBranchGuesser;
use Doctrine\Website\Git\TagReader;
use Doctrine\Website\Projects\ProjectVersionsReader;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProjectVersionsReaderTest extends TestCase
{
    private TagReader&MockObject $tagReader;

    private TagBranchGuesser&MockObject $tagBranchGuesser;

    private ProjectVersionsReader $projectVersionsReader;

    public function testReadProjectVersions(): void
    {
        $repositoryPath = '/repository/path';

        $tag1 = new Tag('v2.0.0', new DateTimeImmutable());
        $tag2 = new Tag('v2.0.1', new DateTimeImmutable());
        $tag3 = new Tag('v3.0.0', new DateTimeImmutable());
        $tag4 = new Tag('dev-test', new DateTimeImmutable()); // will be removed
        $tag5 = new Tag('0.1.0', new DateTimeImmutable());
        $tag6 = new Tag('0.0.1', new DateTimeImmutable());

        $tags = [$tag1, $tag2, $tag3, $tag4, $tag5, $tag6];

        $this->tagReader->expects(self::once())
            ->method('getRepositoryTags')
            ->with($repositoryPath)
            ->willReturn($tags);

        $this->tagBranchGuesser->expects(self::exactly(5))
            ->method('generateTagBranchSlug')
            ->willReturnMap([
                [$tag1, '2.0'],
                [$tag2, '2.0'],
                [$tag3, '3.0'],
                [$tag5, '0.1'],
                [$tag6, '0.0'],
            ]);

        $this->tagBranchGuesser->expects(self::exactly(5))
            ->method('guessTagBranchName')
            ->willReturnMap([
                [$repositoryPath, $tag1, '2.0'],
                [$repositoryPath, $tag2, '2.0'],
                [$repositoryPath, $tag3, '3.0'],
                [$repositoryPath, $tag5, '0.1'],
                [$repositoryPath, $tag6, null],
            ]);

        $versions = $this->projectVersionsReader->readProjectVersions(
            $repositoryPath,
        );

        $expected = [
            [
                'name' => '2.0',
                'slug' => '2.0',
                'branchName' => '2.0',
                'tags' => [$tag1, $tag2],
            ],
            [
                'name' => '3.0',
                'slug' => '3.0',
                'branchName' => '3.0',
                'tags' => [$tag3],
            ],
            [
                'name' => '0.1',
                'slug' => '0.1',
                'branchName' => '0.1',
                'tags' => [$tag5],
            ],
        ];

        self::assertSame($expected, $versions);
    }

    public function testReadProjectVersionsSkipMissingBranchSlug(): void
    {
        $repositoryPath = '/repository/path';

        $tag1 = new Tag('tag', new DateTimeImmutable());
        $tag2 = new Tag('v2.0.0', new DateTimeImmutable());

        $tags = [$tag1, $tag2];

        $this->tagReader->expects(self::once())
            ->method('getRepositoryTags')
            ->with($repositoryPath)
            ->willReturn($tags);

        $this->tagBranchGuesser->expects(self::exactly(2))
            ->method('generateTagBranchSlug')
            ->willReturnMap([
                [$tag1, null],
                [$tag2, '2.0'],
            ]);

        $this->tagBranchGuesser->expects(self::once())
            ->method('guessTagBranchName')
            ->with($repositoryPath, $tag2)
            ->willReturn('2.0');

        $versions = $this->projectVersionsReader->readProjectVersions(
            $repositoryPath,
        );

        $expected = [
            [
                'name' => '2.0',
                'slug' => '2.0',
                'branchName' => '2.0',
                'tags' => [$tag2],
            ],
        ];

        self::assertSame($expected, $versions);
    }

    protected function setUp(): void
    {
        $this->tagReader        = $this->createMock(TagReader::class);
        $this->tagBranchGuesser = $this->createMock(TagBranchGuesser::class);

        $this->projectVersionsReader = new ProjectVersionsReader(
            $this->tagReader,
            $this->tagBranchGuesser,
        );
    }
}
