<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataBuilder\ProjectDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteData;
use Doctrine\Website\DataBuilder\WebsiteDataReader;
use Doctrine\Website\DataSources\Projects;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProjectsTest extends TestCase
{
    private WebsiteDataReader&MockObject $dataReader;

    private Projects $projects;

    protected function setUp(): void
    {
        $this->dataReader = $this->createMock(WebsiteDataReader::class);

        $this->projects = new Projects(
            $this->dataReader,
        );
    }

    public function testGetSourceRows(): void
    {
        $expected = [
            [
                'active' => true,
                'archived' => false,
                'integration' => false,
                'name' => 'Object Relational Mapper',
                'repositoryName' => 'doctrine2',
                'versions' => [],
            ],
            [
                'active' => true,
                'archived' => false,
                'integration' => false,
                'name' => 'Database Abstraction Layer',
                'repositoryName' => 'dbal',
                'versions' => [],
            ],
        ];

        $this->dataReader->expects(self::once())
            ->method('read')
            ->with(ProjectDataBuilder::DATA_FILE)
            ->willReturn(new WebsiteData('test', $expected));

        $projectRows = $this->projects->getSourceRows();

        self::assertSame($expected, $projectRows);
    }
}
