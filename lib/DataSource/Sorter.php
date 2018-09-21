<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSource;

use RuntimeException;
use function count;
use function is_string;
use function sprintf;
use function strtolower;

class Sorter
{
    /** @var int */
    private $level = 0;

    /** @var string[] */
    private $fields;

    /** @var int[] */
    private $orders;

    /**
     * @param int[]|string[] $orderBy
     */
    public function __construct(array $orderBy)
    {
        foreach ($orderBy as $field => $order) {
            $this->fields[] = $field;

            $sortOrder = is_string($order)
                ? strtolower($order) === 'asc' ? 1 : -1
                : $order;

            $this->orders[] = $sortOrder;
        }
    }

    /**
     * @param mixed[] $a
     * @param mixed[] $b
     */
    public function __invoke(array $a, array $b) : int
    {
        $returnVal        = 0;
        $comparisonField  = $this->fields[$this->level];
        $order            = $this->orders[$this->level];
        $aComparisonField = $this->getComparisonField($a, $comparisonField);
        $bComparisonField = $this->getComparisonField($b, $comparisonField);

        $comparisonResult = $aComparisonField <=> $bComparisonField;

        if ($comparisonResult !== 0) {
            $returnVal = $comparisonResult;
        } else {
            if ($this->level < count($this->fields) - 1) {
                $this->level++;

                return $this->__invoke($a, $b);
            }
        }

        $returnVal *= $order;

        $this->level = 0;

        return $returnVal;
    }

    /**
     * @param mixed[] $item
     *
     * @return mixed
     */
    private function getComparisonField(array $item, string $field)
    {
        if (! isset($item[$field])) {
            throw new RuntimeException(sprintf('Unable to find comparison field %s', $field));
        }

        $value = $item[$field];

        return is_string($value) ? strtolower($value) : $value;
    }
}
