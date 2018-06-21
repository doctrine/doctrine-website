<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectFactory;
use Doctrine\Website\Projects\ProjectRepository;
use PHPUnit\Framework\TestCase;

class ProjectRepositoryTest extends TestCase
{
    /** @var string[][] */
    private $projects = [
        [
            'name' => 'ORM',
            'slug' => 'orm',
            'docsSlug' => 'doctrine-orm',
        ],
        [
            'name' => 'DBAL',
            'slug' => 'dbal',
            'docsSlug' => 'doctrine-dbal',
        ],
    ];

    /** @var ProjectFactory */
    private $projectFactory;

    /** @var ProjectRepository */
    private $projectRepository;

    protected function setUp() : void
    {
        $this->projectFactory    = new ProjectFactory();
        $this->projectRepository = new ProjectRepository($this->projects, $this->projectFactory);
    }

    public function testFindOneBySlug() : void
    {
        $orm = $this->projectRepository->findOneBySlug('orm');

        $this->assertInstanceOf(Project::class, $orm);
        $this->assertEquals('ORM', $orm->getName());

        $orm = $this->projectRepository->findOneBySlug('doctrine-orm');

        $this->assertInstanceOf(Project::class, $orm);
        $this->assertEquals('ORM', $orm->getName());

        $dbal = $this->projectRepository->findOneBySlug('dbal');

        $this->assertInstanceOf(Project::class, $dbal);
        $this->assertEquals('DBAL', $dbal->getName());

        $dbal = $this->projectRepository->findOneBySlug('doctrine-dbal');

        $this->assertInstanceOf(Project::class, $dbal);
        $this->assertEquals('DBAL', $dbal->getName());
    }

    public function testFindAll() : void
    {
        $this->assertCount(2, $this->projectRepository->findAll());
    }
}
