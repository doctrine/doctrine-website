<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class PartnerDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    /** @param string[] $items */
    public function __construct(
        #[ORM\Column(type: 'string')]
        private string $label,
        #[ORM\Column(type: 'simple_array', nullable: true)]
        private array $items,
    ) {
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
