<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class EventType
{
    public const WEBINAR    = 'webinar';
    public const CONFERENCE = 'conference';

    private function __construct()
    {
    }
}
