<?php

declare(strict_types=1);

namespace Doctrine\Website\Builder;

class SourceFileParameters
{
    /** @var mixed[] */
    private $parameters = [];

    /**
     * @param mixed[] $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return mixed[]
     */
    public function getAll() : array
    {
        return $this->parameters;
    }

    /**
     * @return mixed
     */
    public function getParameter(string $key)
    {
        return $this->parameters[$key] ?? null;
    }
}
