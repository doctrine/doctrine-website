<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Tests\TestCase;
use Doctrine\Website\Twig\ProjectExtension;
use PHPUnit\Framework\MockObject\MockObject;

class ProjectExtensionTest extends TestCase
{
    /** @var ProjectRepository|MockObject */
    private $projectRepository;

    /** @var ProjectExtension|MockObject */
    private $projectExtension;

    protected function setUp() : void
    {
        $this->projectRepository = $this->createMock(ProjectRepository::class);

        $this->projectExtension = $this->getMockBuilder(ProjectExtension::class)
            ->setConstructorArgs([
                $this->projectRepository,
                '',
            ])
            ->setMethods(['fileExists'])
            ->getMock();
    }

    public function testGetUrlVersion() : void
    {
        $version = new ProjectVersion(['slug' => '2.0']);

        $this->projectExtension->expects(self::once())
            ->method('fileExists')
            ->with('/test/2.0')
            ->willReturn(true);

        self::assertSame(
            '/test/2.0',
            $this->projectExtension->getUrlVersion($version, '/test/1.0', '1.0')
        );
    }

    public function testGetUrlVersionCurrent() : void
    {
        $version = new ProjectVersion(['slug' => '2.0']);

        $this->projectExtension->expects(self::once())
            ->method('fileExists')
            ->with('/test/2.0')
            ->willReturn(true);

        self::assertSame(
            '/test/2.0',
            $this->projectExtension->getUrlVersion($version, '/test/current', '1.0')
        );
    }

    public function testGetUrlVersionNotFound() : void
    {
        $version = new ProjectVersion(['slug' => '2.0']);

        $this->projectExtension->expects(self::once())
            ->method('fileExists')
            ->with('/test/2.0')
            ->willReturn(false);

        self::assertNull($this->projectExtension->getUrlVersion($version, '/test/1.0', '1.0'));
    }
}
