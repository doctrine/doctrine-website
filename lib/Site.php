<?php

declare(strict_types=1);

namespace Doctrine\Website;

class Site
{
    /** @var string */
    private $title;

    /** @var string */
    private $subtitle;

    /** @var string */
    private $url;

    /** @var string[] */
    private $keywords;

    /** @var string */
    private $description;

    /** @var string */
    private $env;

    /** @var string */
    private $googleAnalyticsTrackingId;

    /**
     * @param string[] $keywords
     */
    public function __construct(
        string $title,
        string $subtitle,
        string $url,
        array $keywords,
        string $description,
        string $env,
        string $googleAnalyticsTrackingId
    ) {
        $this->title                     = $title;
        $this->subtitle                  = $subtitle;
        $this->url                       = $url;
        $this->keywords                  = $keywords;
        $this->description               = $description;
        $this->env                       = $env;
        $this->googleAnalyticsTrackingId = $googleAnalyticsTrackingId;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getSubtitle() : string
    {
        return $this->subtitle;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * @return string[]
     */
    public function getKeywords() : array
    {
        return $this->keywords;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getEnv() : string
    {
        return $this->env;
    }

    public function googleAnalyticsTrackingId() : string
    {
        return $this->googleAnalyticsTrackingId;
    }
}
