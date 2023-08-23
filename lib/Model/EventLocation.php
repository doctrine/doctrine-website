<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class EventLocation
{
    public function __construct(private string $name, private Address $address)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}
