<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Doctrine\Website\StaticGenerator\Site as BaseSite;

final class Site extends BaseSite
{
    /** @param string[] $keywords */
    public function __construct(
        string $title,
        string $subtitle,
        string $url,
        array $keywords,
        string $description,
        string $env,
        string $googleAnalyticsTrackingId,
        private readonly string $assetsUrl,
    ) {
        parent::__construct(
            $title,
            $subtitle,
            $url,
            $keywords,
            $description,
            $env,
            $googleAnalyticsTrackingId,
        );
    }

    public function getAssetsUrl(): string
    {
        return $this->assetsUrl;
    }
}
