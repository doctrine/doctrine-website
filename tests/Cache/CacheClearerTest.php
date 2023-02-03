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

    private string $rootDir;

    private string $env;

    private CacheClearer&MockObject $cacheClearer;

    public function testClear(): void
    {
        $buildDir = __DIR__;

        $this->cacheClearer->expects(self::exactly(3))
            ->method('glob')
            ->willReturnMap([
                [__DIR__, [__DIR__]],
                [__DIR__, [__DIR__]],
                [__DIR__ . '/source/projects/*', [__DIR__]],
                [__DIR__ . '/cache/*', [__DIR__]],
            ]);

        $dirs = $this->cacheClearer->clear($buildDir);

        self::assertSame([
            __DIR__,
            __DIR__,
            __DIR__,
        ], $dirs);
    }

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->rootDir    = __DIR__;
        $this->env        = 'test';

        $this->cacheClearer = $this->getMockBuilder(CacheClearer::class)
            ->setConstructorArgs([
                $this->filesystem,
                $this->rootDir,
                $this->env,
            ])
            ->setMethods(['glob'])
            ->getMock();
    }
}
