<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class EventLocation
{
    /** @var string */
    private $name;

    /** @var Address */
    private $address;

    public function __construct(string $name, Address $address)
    {
        $this->name    = $name;
        $this->address = $address;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getAddress() : Address
    {
        return $this->address;
    }
}
