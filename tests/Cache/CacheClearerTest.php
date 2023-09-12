<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Cache;

use Doctrine\Website\Cache\CacheClearer;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Filesystem\Filesystem;

class CacheClearerTest extends TestCase
{
    private Filesystem&MockObject $filesystem;

    private string $rootDir = __DIR__ . '/root';

    private CacheClearer $cacheClearer;

    public function testClear(): void
    {
        $this->filesystem->expects(self::exactly(3))
            ->method('remove');

        $buildDir = $this->rootDir . '/build-dir';

        $dirs = $this->cacheClearer->clear($buildDir);

        $expected = [
            $this->rootDir . '/build-dir',
            $this->rootDir . '/source/projects/dbal',
            $this->rootDir . '/cache/data',
        ];

        self::assertSame($expected, $dirs);
    }

    private function createFiles(): void
    {
        $fileystem = new Filesystem();
        $fileystem->dumpFile($this->rootDir . '/source/projects/dbal/file1.txt', '');
        $fileystem->dumpFile($this->rootDir . '/cache/data/projects.json', '{}');
        $fileystem->dumpFile($this->rootDir . '/build-dir/foo/bar/baz.txt', 'qux');
    }

    protected function setUp(): void
    {
        $this->createFiles();

        $this->filesystem = $this->createMock(Filesystem::class);

        $this->cacheClearer = new CacheClearer($this->filesystem, $this->rootDir);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->rootDir);
    }
}
