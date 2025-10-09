<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

use function array_merge;

class SourceFileParameters
{
    /** @param mixed[] $parameters */
    public function __construct(private array $parameters = [])
    {
    }

    /** @return mixed[] */
    public function getAll(): array
    {
        return $this->parameters;
    }

    public function getParameter(string $key): mixed
    {
        return $this->parameters[$key] ?? null;
    }

    public function setParameter(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    /** @param mixed[] $parameters */
    public function merge(array $parameters): void
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }
}
