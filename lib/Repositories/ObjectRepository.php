<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use UnexpectedValueException;

/**
 * Contract for a Doctrine persistence layer ObjectRepository class to implement.
 *
 * @template-covariant T of object
 */
interface ObjectRepository
{
    /**
     * Finds an object by its identifier.
     *
     * @return T|null
     */
    public function find(mixed $id): object|null;

    /**
     * Finds all objects in the repository.
     *
     * @return T[]
     */
    public function findAll(): array;

    /**
     * Finds objects by a set of criteria.
     *
     * @param array<string, mixed>                          $criteria
     * @param array<string, 'asc'|'desc'|'ASC'|'DESC'>|null $orderBy
     *
     * @return T[]
     *
     * @throws UnexpectedValueException
     */
    public function findBy(
        array $criteria,
        array|null $orderBy = null,
    ): array;

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array<string, mixed> $criteria
     *
     * @return T|null
     */
    public function findOneBy(array $criteria): object|null;
}
