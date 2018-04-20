<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectVersion;
use PHPUnit\Framework\TestCase;

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
            'wip' => true,
        ]);
    }

    public function testGetName() : void
    {
        $this->assertEquals('1.0', $this->projectVersion->getName());
        $this->assertEquals('1.0', $this->projectVersion->getBranchName());
        $this->assertEquals('1.0', $this->projectVersion->getSlug());
        $this->assertTrue($this->projectVersion->isCurrent());
        $this->assertTrue($this->projectVersion->isWip());
    }
}
