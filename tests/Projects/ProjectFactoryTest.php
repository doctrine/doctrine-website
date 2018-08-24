<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectFactory;
use PHPUnit\Framework\TestCase;

class ProjectFactoryTest extends TestCase
{
    /** @var ProjectFactory */
    private $projectFactory;

    protected function setUp() : void
    {
        $this->projectFactory = new ProjectFactory();
    }

    public function testCreate() : void
    {
        $project = $this->projectFactory->create(['slug' => 'test']);

        self::assertSame('test', $project->getSlug());
    }
}
