<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Request;

class ArrayRequestCollection implements RequestCollection
{
    /** @param mixed[] $requests */
    public function __construct(private array $requests)
    {
    }

    /** @return mixed[] */
    public function getRequests(): iterable
    {
        return $this->requests;
    }
}
