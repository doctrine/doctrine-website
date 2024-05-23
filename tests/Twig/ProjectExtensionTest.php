<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Tests\TestCase;
use Doctrine\Website\Twig\ProjectExtension;
use org\bovigo\vfs\vfsStream;

class ProjectExtensionTest extends TestCase
{
    private ProjectExtension $projectExtension;

    protected function setUp(): void
    {
        $projectRepository = $this->createMock(ProjectRepository::class);

        $structure = [
            'test' => [
                '1.0' => [],
                '2.0' => [],
            ],
        ];
        vfsStream::setup('root', null, $structure);

        $sourceDir = vfsStream::url('root');

        $this->projectExtension = new ProjectExtension($projectRepository, $sourceDir);
    }

    public function testGetUrlVersion(): void
    {
        $version = new ProjectVersion(['slug' => '2.0']);

        self::assertSame(
            '/test/2.0',
            $this->projectExtension->getUrlVersion($version, '/test/1.0', '1.0'),
        );
    }

    public function testGetUrlVersionCurrent(): void
    {
        $version = new ProjectVersion(['slug' => '2.0']);

        self::assertSame(
            '/test/2.0',
            $this->projectExtension->getUrlVersion($version, '/test/current', '1.0'),
        );
    }

    public function testGetUrlVersionNotFound(): void
    {
        $version = new ProjectVersion(['slug' => '1.42']);

        self::assertNull($this->projectExtension->getUrlVersion($version, '/test/1.0', '1.0'));
    }
}
