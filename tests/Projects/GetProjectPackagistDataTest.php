<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\GetProjectPackagistData;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class GetProjectPackagistDataTest extends TestCase
{
    private string $packagistUrl;

    protected function setUp(): void
    {
        vfsStream::setup('url', null, [
            'packages' => [
                'orm.json' => '{}',
                'broken.json' => '{',
            ],
        ]);

        $this->packagistUrl = vfsStream::url('url') . '/packages/%s.json';
    }

    public function testFetchingPackagistData(): void
    {
        $projectPackagistData = new GetProjectPackagistData($this->packagistUrl);

        self::assertSame([], $projectPackagistData('orm'));
    }

    public function testInvalidJson(): void
    {
        $projectPackagistData = new GetProjectPackagistData($this->packagistUrl);

        self::assertSame([], $projectPackagistData('broken'));
    }
}
