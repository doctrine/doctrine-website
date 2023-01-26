<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

class WebsiteData
{
    /** @param mixed[] $data */
    public function __construct(private string $name, private array $data)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return mixed[] */
    public function getData(): array
    {
        return $this->data;
    }
}
