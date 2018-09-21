<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSource;

use Doctrine\SkeletonMapper\DataRepository\BasicObjectDataRepository;
use Doctrine\SkeletonMapper\ObjectManagerInterface;
use function array_slice;
use function in_array;
use function usort;

class DataSourceObjectDataRepository extends BasicObjectDataRepository
{
    /** @var DataSource */
    private $dataSource;

    /** @var mixed[][]|null */
    private $data;

    public function __construct(
        ObjectManagerInterface $objectManager,
        DataSource $dataSource,
        string $className
    ) {
        parent::__construct($objectManager, $className);
        $this->dataSource = $dataSource;
    }

    /**
     * @return mixed[][]
     */
    public function findAll() : array
    {
        return $this->getData();
    }

    /**
     * @param mixed[] $criteria
     * @param mixed[] $orderBy
     *
     * @return mixed[][]
     */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ) : array {
        $objects = [];

        foreach ($this->getData() as $object) {
            if (! $this->matches($criteria, $object)) {
                continue;
            }

            $objects[] = $object;
        }

        if ($orderBy !== null && $orderBy !== []) {
            $objects = $this->sort($objects, $orderBy);
        }

        if ($limit !== null || $offset !== null) {
            if ($offset === null) {
                $offset = 0;
            }

            return array_slice($objects, $offset, $limit);
        }

        return $objects;
    }

    /**
     * @param mixed[] $criteria
     *
     * @return null|mixed[]
     */
    public function findOneBy(array $criteria) : ?array
    {
        foreach ($this->getData() as $object) {
            if ($this->matches($criteria, $object)) {
                return $object;
            }
        }

        return null;
    }

    /**
     * @param mixed[] $criteria
     * @param mixed[] $object
     */
    private function matches(array $criteria, array $object) : bool
    {
        $matches = true;

        foreach ($criteria as $key => $value) {
            if (isset($value['$contains'])) {
                if (isset($object[$key]) && in_array($value['$contains'], $object[$key], true)) {
                    continue;
                }
            } else {
                if (isset($object[$key]) && $object[$key] === $value) {
                    continue;
                }
            }

            $matches = false;
        }

        return $matches;
    }

    /**
     * @param mixed[][] $objects
     * @param int[]     $orderBy
     *
     * @return mixed[][] $objects
     */
    private function sort(array $objects, array $orderBy) : array
    {
        $sorter = new Sorter($orderBy);

        usort($objects, $sorter);

        return $objects;
    }

    /**
     * @return mixed[][]
     */
    private function getData() : array
    {
        if ($this->data === null) {
            $this->data = $this->dataSource->getData();
        }

        return $this->data;
    }
}
