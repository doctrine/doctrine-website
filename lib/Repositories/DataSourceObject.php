<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\Website\DataSources\DataSource;

use function array_values;
use function count;
use function md5;
use function serialize;

/** @template T of object */
class DataSourceObject
{
    /** @var array<string, T> */
    private array $objects = [];

    /** @phpstan-param class-string<T> $modelClassName */
    public function __construct(private string $modelClassName, private DataSource $dataSource)
    {
    }

    /** @return T[] */
    public function findAll(): array
    {
        $this->initializeObjects();

        return array_values($this->objects);
    }

    /**
     * @param array<string, mixed>       $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return T[]
     */
    public function findBy(array $criteria, array|null $orderBy = null): array
    {
        // TODO Missing implementation
        $this->initializeObjects();

        return array_values($this->objects);
    }

    private function initializeObjects(): void
    {
        if (count($this->objects) !== 0) {
            return;
        }

        $this->objects = [];

        foreach ($this->dataSource->getSourceRows() as $sourceRow) {
            $key                 = md5(serialize($sourceRow));
            $this->objects[$key] = $this->createObject($sourceRow);
        }
    }

    /**
     * @param mixed[] $data
     *
     * @phpstan-return T
     */
    private function createObject(array $data): object
    {
        return new $this->modelClassName(...$data);
    }
}
