<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

/**
 * @template T of object
 * @template-implements ObjectRepository<T>
 */
class BaseObjectRepository implements ObjectRepository
{
    /** @param DataSourceObject<T> $dataSourceObject */
    public function __construct(private DataSourceObject $dataSourceObject)
    {
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * {@inheritDoc}
     *
     * @psalm-return T|null
     */
    public function find(mixed $id): object|null
    {
        // TODO define what's the id of the data source
        $result = $this->findBy(['id' => $id]);

        return $result[0] ?? null;
    }

    /**
     * Finds all objects in the repository.
     *
     * @psalm-return T[]
     */
    public function findAll(): array
    {
        return $this->dataSourceObject->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array|null $orderBy = null): array
    {
        return $this->dataSourceObject->findBy(
            $criteria,
            $orderBy,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria): object|null
    {
        $result = $this->dataSourceObject->findBy($criteria);

        return $result[0] ?? null;
    }
}
