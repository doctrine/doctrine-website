<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectFactory;
use Doctrine\Website\Projects\ProjectRepository;
use Doctrine\Website\Tests\TestCase;

class ProjectRepositoryTest extends TestCase
{
    /** @var string[] */
    private $projects = ['doctrine2', 'dbal'];

    /** @var ProjectFactory */
    private $projectFactory;

    /** @var ProjectRepository */
    private $projectRepository;

    protected function setUp() : void
    {
        $this->projectFactory = $this->createMock(ProjectFactory::class);

        $project1 = new Project([
            'name' => 'ORM',
            'slug' => 'orm',
            'docsSlug' => 'doctrine-orm',
        ]);

        $project2 = new Project([
            'name' => 'DBAL',
            'slug' => 'dbal',
            'docsSlug' => 'doctrine-dbal',
        ]);

        $this->projectFactory->expects(self::at(0))
            ->method('create')
            ->with('doctrine2')
            ->willReturn($project1);

        $this->projectFactory->expects(self::at(1))
            ->method('create')
            ->with('dbal')
            ->willReturn($project2);

        $this->projectRepository = new ProjectRepository($this->projects, $this->projectFactory);
    }

    public function testFindOneBySlug() : void
    {
        $orm = $this->projectRepository->findOneBySlug('orm');

        self::assertSame('ORM', $orm->getName());

        $orm = $this->projectRepository->findOneBySlug('doctrine-orm');

        self::assertSame('ORM', $orm->getName());

        $dbal = $this->projectRepository->findOneBySlug('dbal');

        self::assertSame('DBAL', $dbal->getName());

        $dbal = $this->projectRepository->findOneBySlug('doctrine-dbal');

        self::assertSame('DBAL', $dbal->getName());
    }

    public function testFindAll() : void
    {
        self::assertCount(2, $this->projectRepository->findAll());
    }
}
