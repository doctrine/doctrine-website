<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataSources\ArrayDataSource;
use Doctrine\Website\Tests\TestCase;

class ArrayDataSourceTest extends TestCase
{
    public function testGetSourceRows() : void
    {
        $rows = [
            [
                'name' => 'Symfony',
                'url' => 'https://symfony.com',
            ],
            [
                'name' => 'Laravel',
                'url' => 'https://laravel.com',
            ],
        ];

        $dataSource = new ArrayDataSource($rows);

        self::assertEquals($rows, $dataSource->getSourceRows());
    }
}
