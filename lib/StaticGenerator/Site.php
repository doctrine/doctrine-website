<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator;

class Site
{
    /** @param string[] $keywords */
    public function __construct(
        private string $title,
        private string $subtitle,
        private string $url,
        private array $keywords,
        private string $description,
        private string $env,
        private string $googleAnalyticsTrackingId,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /** @return string[] */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEnv(): string
    {
        return $this->env;
    }

    public function googleAnalyticsTrackingId(): string
    {
        return $this->googleAnalyticsTrackingId;
    }
}
