<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\Projects\ProjectVersions;
use PHPUnit\Framework\TestCase;

class ProjectVersionsTest extends TestCase
{
    public function testIterator() : void
    {
        $versions = new ProjectVersions([[], []]);

        $this->assertCount(2, $versions);

        $versions = new ProjectVersions([
            new ProjectVersion([]),
            new ProjectVersion([]),
        ]);

        $this->assertCount(2, $versions);
    }
}
