<?php

declare(strict_types=1);

namespace Doctrine\Website;

use Doctrine\StaticWebsiteGenerator\Site as BaseSite;

final class Site extends BaseSite
{
    /** @var string */
    private $assetsUrl;

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
        string $googleAnalyticsTrackingId,
        string $assetsUrl
    ) {
        parent::__construct(
            $title,
            $subtitle,
            $url,
            $keywords,
            $description,
            $env,
            $googleAnalyticsTrackingId
        );

        $this->assetsUrl = $assetsUrl;
    }

    public function getAssetsUrl(): string
    {
        return $this->assetsUrl;
    }
}
