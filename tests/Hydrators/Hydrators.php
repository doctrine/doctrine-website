<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use Doctrine\Common\EventManager;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataFactory;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInstantiator;
use Doctrine\SkeletonMapper\ObjectIdentityMap;
use Doctrine\SkeletonMapper\ObjectManager;
use Doctrine\SkeletonMapper\ObjectRepository\ObjectRepositoryFactory;
use Doctrine\SkeletonMapper\ObjectRepository\ObjectRepositoryInterface;
use Doctrine\SkeletonMapper\Persister\ObjectPersisterFactory;
use Doctrine\Website\Model\Contributor;
use Doctrine\Website\Repositories\ContributorRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

abstract class Hydrators extends TestCase
{
    /**
     * @param class-string<T> $className
     *
     * @return T
     *
     * @template T
     */
    protected function createHydrator(string $className)
    {
        $objectRepositoryFactory = new ObjectRepositoryFactory();

        /** @var ObjectRepositoryInterface<object>&MockObject $contributorRepository */
        $contributorRepository = $this->createMock(ContributorRepository::class);
        $contributorRepository->method('findOneByGithub')->willReturn(new Contributor());

        $objectRepositoryFactory->addObjectRepository(Contributor::class, $contributorRepository);

        $objectManager = new ObjectManager(
            $objectRepositoryFactory,
            new ObjectPersisterFactory(),
            new ObjectIdentityMap($objectRepositoryFactory),
            new ClassMetadataFactory(new ClassMetadataInstantiator()),
            new EventManager(),
        );

        return new $className($objectManager);
    }

    /** @param array<string, mixed> $data */
    protected function populate(object $model, array $data): void
    {
        $modelRef = new ReflectionObject($model);

        foreach ($data as $property => $value) {
            $reflectionProperty = $modelRef->getProperty($property);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($model, $value);
        }
    }
}
