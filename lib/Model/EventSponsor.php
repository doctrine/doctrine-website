<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class EventSponsor
{
    public function __construct(private string $name, private string $url, private string $logo, private UtmParameters $utmParameters)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /** @param string[] $parameters */
    public function getUrlWithUtmParameters(array $parameters = []): string
    {
        return $this->utmParameters->buildUrl($this->url, $parameters);
    }

    public function getLogo(): string
    {
        return $this->logo;
    }
}
