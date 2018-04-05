<?php

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectFactory;
use PHPUnit\Framework\TestCase;

class ProjectFactoryTest extends TestCase
{
    /** @var ProjectFactory */
    private $projectFactory;

    protected function setUp()
    {
        $this->projectFactory = new ProjectFactory();
    }

    public function testCreate()
    {
        $project = $this->projectFactory->create([]);

        $this->assertInstanceOf(Project::class, $project);
    }
}
