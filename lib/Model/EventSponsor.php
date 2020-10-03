<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class EventSponsor
{
    /** @var string */
    private $name;

    /** @var string */
    private $url;

    /** @var string */
    private $logo;

    /** @var UtmParameters */
    private $utmParameters;

    public function __construct(string $name, string $url, string $logo, UtmParameters $utmParameters)
    {
        $this->name          = $name;
        $this->url           = $url;
        $this->logo          = $logo;
        $this->utmParameters = $utmParameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string[] $parameters
     */
    public function getUrlWithUtmParameters(array $parameters = []): string
    {
        return $this->utmParameters->buildUrl($this->url, $parameters);
    }

    public function getLogo(): string
    {
        return $this->logo;
    }
}
