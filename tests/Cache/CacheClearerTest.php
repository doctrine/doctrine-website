<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Cache;

use Doctrine\Website\Cache\CacheClearer;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Filesystem\Filesystem;

class CacheClearerTest extends TestCase
{
    /** @var Filesystem|MockObject */
    private $filesystem;

    /** @var string */
    private $rootDir;

    /** @var string */
    private $env;

    /** @var CacheClearer|MockObject */
    private $cacheClearer;

    public function testClear() : void
    {
        $buildDir = __DIR__;

        $this->cacheClearer->expects(self::at(0))
            ->method('glob')
            ->with(__DIR__)
            ->willReturn([__DIR__]);

        $this->cacheClearer->expects(self::at(1))
            ->method('glob')
            ->with(__DIR__ . '/source/projects/*')
            ->willReturn([__DIR__]);

        $this->cacheClearer->expects(self::at(2))
            ->method('glob')
            ->with(__DIR__ . '/cache/*')
            ->willReturn([__DIR__]);

        $dirs = $this->cacheClearer->clear($buildDir);

        self::assertSame([
            __DIR__,
            __DIR__,
            __DIR__,
        ], $dirs);
    }

    protected function setUp() : void
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
