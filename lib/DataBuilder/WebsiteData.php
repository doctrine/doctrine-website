<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

class WebsiteData
{
    /** @var string */
    private $name;

    /** @var mixed[] */
    private $data;

    /** @param mixed[] $data */
    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->data = $data;
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
