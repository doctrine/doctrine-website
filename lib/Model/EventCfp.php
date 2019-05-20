<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use LogicException;
use function sprintf;

final class EventCfp
{
    /** @var string */
    private $googleFormId;

    /** @var DateTimeRange */
    private $dateTimeRange;

    public function __construct(string $googleFormId, DateTimeRange $dateTimeRange)
    {
        $this->googleFormId  = $googleFormId;
        $this->dateTimeRange = $dateTimeRange;
    }

    public function exists() : bool
    {
        return $this->googleFormId !== '';
    }

    public function getGoogleFormUrl() : string
    {
        if (! $this->exists()) {
            throw new LogicException('Cannot call EventCfp::getGoogleFormUrl() when no googleFormId is set.');
        }

        return sprintf('https://docs.google.com/forms/d/e/%s/viewform', $this->googleFormId);
    }

    public function getEmbeddedGoogleFormUrl() : string
    {
        return sprintf('%s?embedded=true', $this->getGoogleFormUrl());
    }

    public function getDates() : DateTimeRange
    {
        return $this->dateTimeRange;
    }
}
