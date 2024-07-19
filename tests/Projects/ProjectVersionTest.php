<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use DateTimeImmutable;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;

class ProjectVersionTest extends TestCase
{
    private ProjectVersion $projectVersion;

    protected function setUp(): void
    {
        $this->projectVersion = new ProjectVersion([
            'name' => '1.0',
            'branchName' => '1.0',
            'slug' => '1.0',
            'current' => true,
            'upcoming' => true,
            'aliases' => ['alias'],
        ]);
    }

    public function testGetName(): void
    {
        self::assertSame('1.0', $this->projectVersion->getName());
    }

    public function testGetBranchName(): void
    {
        self::assertSame('1.0', $this->projectVersion->getBranchName());
    }

    public function testGetSlug(): void
    {
        self::assertSame('1.0', $this->projectVersion->getSlug());
    }

    public function testIsCurrent(): void
    {
        self::assertTrue($this->projectVersion->isCurrent());
    }

    public function testIsUpcoming(): void
    {
        self::assertTrue($this->projectVersion->isUpcoming());
    }

    public function testGetAliases(): void
    {
        self::assertSame(['alias', 'current', 'stable'], $this->projectVersion->getAliases());
    }

    public function testDefaults(): void
    {
        $projectVersion = new ProjectVersion(['name' => '1.0']);

        self::assertSame('1.0', $projectVersion->getName());
        self::assertNull($projectVersion->getBranchName());
        self::assertSame('1.0', $projectVersion->getSlug());
        self::assertFalse($projectVersion->isCurrent());
        self::assertFalse($projectVersion->isUpcoming());
        self::assertTrue($projectVersion->hasDocs());
        self::assertEmpty($projectVersion->getTags());
        self::assertEmpty($projectVersion->getDocsLanguages());
        self::assertEmpty($projectVersion->getAliases());
    }

    public function testHasBranchName(): void
    {
        $projectVersion = new ProjectVersion([]);

        self::assertFalse($projectVersion->hasBranchName());

        $projectVersion = new ProjectVersion(['branchName' => '1.0']);

        self::assertTrue($projectVersion->hasBranchName());
    }

    public function testHasTags(): void
    {
        $projectVersion = new ProjectVersion([]);

        self::assertFalse($projectVersion->hasTags());

        $projectVersion = new ProjectVersion([]);
        $projectVersion->addTag(new Tag('1.0', new DateTimeImmutable('2000-01-01')));

        self::assertTrue($projectVersion->hasTags());
    }
}
