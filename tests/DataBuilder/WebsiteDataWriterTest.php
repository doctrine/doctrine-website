<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataBuilder;

use Doctrine\Website\DataBuilder\WebsiteData;
use Doctrine\Website\DataBuilder\WebsiteDataWriter;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class WebsiteDataWriterTest extends TestCase
{
    public function testWrite(): void
    {
        vfsStream::setup('cache', null, [
            'data' => [],
            'expected.json' => '{"key": "value"}',
        ]);
        $cacheRoot = vfsStream::url('cache');

        $websiteDataWriter = new WebsiteDataWriter($cacheRoot);

        $websiteDataWriter->write(new WebsiteData('foo', ['key' => 'value']));

        $filePath         = $cacheRoot . '/data/foo.json';
        $expectedFilePath = $cacheRoot . '/expected.json';

        self::assertFileExists($filePath);
        self::assertJsonFileEqualsJsonFile($expectedFilePath, $filePath);
    }

    public function testWriteCreatesDataDirectoryWhenMissing(): void
    {
        vfsStream::setup('cache');
        $cacheRoot = vfsStream::url('cache');

        $websiteDataWriter = new WebsiteDataWriter($cacheRoot);

        self::assertDirectoryDoesNotExist($cacheRoot . '/data');

        $websiteDataWriter->write(new WebsiteData('foo', []));

        self::assertDirectoryExists($cacheRoot . '/data');
    }
}
