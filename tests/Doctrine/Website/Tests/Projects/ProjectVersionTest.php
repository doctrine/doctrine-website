<?php

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectVersion;
use PHPUnit\Framework\TestCase;

class ProjectVersionTest extends TestCase
{
    /** @var ProjectVersion */
    private $projectVersion;

    protected function setUp()
    {
        $this->projectVersion = new ProjectVersion([
            'name' => '1.0',
            'branchName' => '1.0',
            'slug' => '1.0',
            'current' => true,
        ]);
    }

    public function testGetName()
    {
        $this->assertEquals('1.0', $this->projectVersion->getName());
        $this->assertEquals('1.0', $this->projectVersion->getBranchName());
        $this->assertEquals('1.0', $this->projectVersion->getSlug());
        $this->assertTrue($this->projectVersion->isCurrent());
    }
}
