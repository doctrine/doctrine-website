<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Controller;

class Response
{
    /** @param mixed[] $parameters */
    public function __construct(private array $parameters, private string $template = '')
    {
    }

    /** @return mixed[] */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
