<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataSources\Projects;
use Doctrine\Website\Projects\ProjectDataReader;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProjectsTest extends TestCase
{
    /** @var mixed[] */
    private $projectsData = [
        ['repositoryName' => 'doctrine2'],
        ['repositoryName' => 'dbal'],
    ];

    /** @var ProjectDataReader|MockObject */
    private $projectDataReader;

    /** @var Projects */
    private $projects;

    protected function setUp() : void
    {
        $this->projectDataReader = $this->createMock(ProjectDataReader::class);

        $this->projects = new Projects($this->projectDataReader, $this->projectsData);
    }

    public function testGetSourceRows() : void
    {
        $this->projectDataReader->expects(self::at(0))
            ->method('read')
            ->with('doctrine2')
            ->willReturn([
                'name' => 'Object Relational Mapper',
                'repositoryName' => 'doctrine2',
            ]);

        $this->projectDataReader->expects(self::at(1))
            ->method('read')
            ->with('dbal')
            ->willReturn([
                'name' => 'Database Abstraction Layer',
                'repositoryName' => 'dbal',
            ]);

        $projectRows = $this->projects->getSourceRows();

        self::assertSame([
            [
                'active' => true,
                'archived' => false,
                'hasDocs' => true,
                'integration' => false,
                'name' => 'Object Relational Mapper',
                'repositoryName' => 'doctrine2',
            ],
            [
                'active' => true,
                'archived' => false,
                'hasDocs' => true,
                'integration' => false,
                'name' => 'Database Abstraction Layer',
                'repositoryName' => 'dbal',
            ],
        ], $projectRows);
    }
}
