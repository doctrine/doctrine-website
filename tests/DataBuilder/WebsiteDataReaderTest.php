<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataBuilder;

use Doctrine\Website\DataBuilder\WebsiteData;
use Doctrine\Website\DataBuilder\WebsiteDataReader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class WebsiteDataReaderTest extends TestCase
{
    private WebsiteDataReader $websiteDataReader;

    public function testRead(): void
    {
        $websiteData = $this->websiteDataReader->read('valid');

        $expected = new WebsiteData('valid', []);

        self::assertEquals($expected, $websiteData);
    }

    public function testReadInvalidFilePath(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File vfs://cache/data/foo.json does not exist. Run ./doctrine build-website-data to generate.');

        $this->websiteDataReader->read('foo');
    }

    public function testReadInvalidJson(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not load JSON from file vfs://cache/data/invalid.json');

        $this->websiteDataReader->read('invalid');
    }

    protected function setUp(): void
    {
        $structure = [
            'data' => [
                'invalid.json' => '{',
                'valid.json' => '{}',
            ],
        ];
        vfsStream::setup('cache', null, $structure);

        $this->websiteDataReader = new WebsiteDataReader(vfsStream::url('cache'));
    }
}
