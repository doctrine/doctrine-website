<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataSources\DoctrineUsers;
use PHPUnit\Framework\TestCase;

class DoctrineUsersTest extends TestCase
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

        $doctrineUsers = new DoctrineUsers($rows);

        self::assertEquals($rows, $doctrineUsers->getSourceRows());
    }
}
