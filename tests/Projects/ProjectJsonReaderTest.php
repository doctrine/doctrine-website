<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectJsonReader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProjectJsonReaderTest extends TestCase
{
    /** @var ProjectJsonReader */
    private $projectJsonReader;

    public function testRead() : void
    {
        self::assertSame(['shortName' => 'test'], $this->projectJsonReader->read('test-project'));
    }

    public function testReadFileDoesNotExist() : void
    {
        self::assertNull($this->projectJsonReader->read('no-project-json'));
    }

    public function testReadFileHasInvalidJson() : void
    {
        $this->expectException(InvalidArgumentException::class);

        self::assertNull($this->projectJsonReader->read('invalid-project-json'));
    }

    protected function setUp() : void
    {
        $this->projectJsonReader = new ProjectJsonReader(__DIR__ . '/../test-projects');
    }
}
