<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources\DbPrefill;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Website\DataSources\ArrayDataSource;
use Doctrine\Website\DataSources\DbPrefill\SimpleSource;
use PHPUnit\Framework\TestCase;

use function sprintf;

class SimpleSourceTest extends TestCase
{
    public function testSaveDataSouceValuesToDatabase(): void
    {
        $dataSource    = new ArrayDataSource([
            ['id' => 2, 'name' => 'foo2'],
            ['name' => 'foo1', 'id' => 1],
        ]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::exactly(2))
            ->method('persist')
            ->willReturnCallback(static function (Foo $foo): void {
                $foo1 = new Foo(1, 'foo1');
                $foo2 = new Foo(2, 'foo2');
                // phpcs:ignoreFile
                if (($foo == $foo1 || $foo == $foo2)) {
                    return;
                }

                self::fail(sprintf('Unexpected Foo instance with id %d', $foo->id));
            });
        $entityManager->expects(self::once())->method('flush');

        $dbPrefill = new SimpleSource(Foo::class, $dataSource, $entityManager);

        $dbPrefill->populate();
    }
}
