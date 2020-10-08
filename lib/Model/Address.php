<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use function sprintf;

final class Address
{
    /** @var string */
    private $line1;

    /** @var string */
    private $line2;

    /** @var string */
    private $city;

    /** @var string */
    private $state;

    /** @var string */
    private $zipCode;

    /** @var string */
    private $countryCode;

    public function __construct(
        string $line1,
        string $line2,
        string $city,
        string $state,
        string $zipCode,
        string $countryCode
    ) {
        $this->line1       = $line1;
        $this->line2       = $line2;
        $this->city        = $city;
        $this->state       = $state;
        $this->zipCode     = $zipCode;
        $this->countryCode = $countryCode;
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
            $this->countryCode
        );
    }
}
