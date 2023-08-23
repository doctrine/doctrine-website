<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use function sprintf;

final class Address
{
    public function __construct(
        private string $line1,
        private string $line2,
        private string $city,
        private string $state,
        private string $zipCode,
        private string $countryCode,
    ) {
    }

    public function getLine1(): string
    {
        return $this->line1;
    }

    public function getLine2(): string
    {
        return $this->line2;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getString(): string
    {
        return sprintf(
            '%s %s %s, %s %s %s',
            $this->line1,
            $this->line2,
            $this->city,
            $this->state,
            $this->zipCode,
            $this->countryCode,
        );
    }
}
