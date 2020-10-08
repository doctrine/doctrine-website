<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class PartnerDetails
{
    /** @var string */
    private $label;

    /** @var string[] */
    private $items;

    /**
     * @param string[] $items
     */
    public function __construct(string $label, array $items)
    {
        $this->label = $label;
        $this->items = $items;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
