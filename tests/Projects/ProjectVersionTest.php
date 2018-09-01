<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\Tests\TestCase;

class ProjectVersionTest extends TestCase
{
    /** @var ProjectVersion */
    private $projectVersion;

    protected function setUp() : void
    {
        $this->projectVersion = new ProjectVersion([
            'name' => '1.0',
            'branchName' => '1.0',
            'slug' => '1.0',
            'current' => true,
            'upcoming' => true,
        ]);
    }

    public function testGetName() : void
    {
        self::assertSame('1.0', $this->projectVersion->getName());
        self::assertSame('1.0', $this->projectVersion->getBranchName());
        self::assertSame('1.0', $this->projectVersion->getSlug());
        self::assertTrue($this->projectVersion->isCurrent());
        self::assertTrue($this->projectVersion->isUpcoming());
    }
}
