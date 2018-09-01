<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectJsonReader;
use Doctrine\Website\Tests\TestCase;
use InvalidArgumentException;

class ProjectJsonReaderTest extends TestCase
{
    /** @var ProjectJsonReader */
    private $projectJsonReader;

    public function testRead() : void
    {
        self::assertSame([
            'repositoryName' => 'test-project',
            'composerPackageName' => 'doctrine/test-project',
            'description' => 'Test description',
            'keywords' => ['keyword1', 'keyword2'],
            'shortName' => 'test',
        ], $this->projectJsonReader->read('test-project'));
    }

    public function testReadFileDoesNotExist() : void
    {
        self::assertEquals(['repositoryName' => 'no-project-json'], $this->projectJsonReader->read('no-project-json'));
    }

    public function testReadFileHasInvalidJson() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->projectJsonReader->read('invalid-project-json');
    }

    protected function setUp() : void
    {
        $this->projectJsonReader = new ProjectJsonReader(__DIR__ . '/../test-projects');
    }
}
