<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Cache;

use Doctrine\Website\Cache\CacheClearer;
use Doctrine\Website\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;

use function sys_get_temp_dir;

class CacheClearerTest extends TestCase
{
    private string $rootDir;

    private CacheClearer $cacheClearer;

    public function testClear(): void
    {
        $buildDir = $this->rootDir . '/build-dir';

        $dirs = $this->cacheClearer->clear($buildDir);

        $expected = [
            $this->rootDir . '/build-dir',
            $this->rootDir . '/source/projects/dbal',
            $this->rootDir . '/cache/data',
        ];

        self::assertSame($expected, $dirs);
    }

    private function createFiles(string $rootDir): void
    {
        $fileystem = new Filesystem();
        $fileystem->dumpFile($rootDir . '/source/projects/dbal/file1.txt', '');
        $fileystem->dumpFile($rootDir . '/cache/data/projects.json', '{}');
        $fileystem->dumpFile($rootDir . '/build-dir/foo/bar/baz.txt', 'qux');
    }

    protected function setUp(): void
    {
        $this->rootDir = sys_get_temp_dir() . '/root';
        $this->createFiles($this->rootDir);

        $this->cacheClearer = new CacheClearer(new Filesystem(), $this->rootDir);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->rootDir);
    }
}
