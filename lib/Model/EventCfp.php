<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use LogicException;

use function sprintf;

final class EventCfp
{
    public function __construct(private string $googleFormId, private DateTimeRange $dateTimeRange)
    {
    }

    public function exists(): bool
    {
        return $this->googleFormId !== '';
    }

    public function getGoogleFormUrl(): string
    {
        if (! $this->exists()) {
            throw new LogicException('Cannot call EventCfp::getGoogleFormUrl() when no googleFormId is set.');
        }

        return sprintf('https://docs.google.com/forms/d/e/%s/viewform', $this->googleFormId);
    }

    public function getEmbeddedGoogleFormUrl(): string
    {
        return sprintf('%s?embedded=true', $this->getGoogleFormUrl());
    }

    public function getDates(): DateTimeRange
    {
        return $this->dateTimeRange;
    }
}
