<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class PartnerDetails
{
    /** @param string[] $items */
    public function __construct(private string $label, private array $items)
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /** @return string[] */
    public function getItems(): array
    {
        return $this->items;
    }
}
