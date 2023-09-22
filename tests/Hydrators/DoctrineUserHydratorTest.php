<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use Doctrine\Website\Hydrators\DoctrineUserHydrator;
use Doctrine\Website\Model\DoctrineUser;

class DoctrineUserHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator       = $this->createHydrator(DoctrineUserHydrator::class);
        $propertyValues = [
            'name' => 'name',
            'url' => 'url',
        ];

        $expected = new DoctrineUser();
        $this->populate($expected, $propertyValues);

        $doctrineUser = new DoctrineUser();

        $hydrator->hydrate($doctrineUser, $propertyValues);

        self::assertEquals($expected, $doctrineUser);
    }
}
