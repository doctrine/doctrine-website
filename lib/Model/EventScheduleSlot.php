<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;

final class EventScheduleSlot
{
    /** @var EventSpeaker */
    private $speaker;

    /** @var DateTimeImmutable */
    private $startDate;

    /** @var DateTimeImmutable */
    private $endDate;

    public function __construct(
        EventSpeaker $speaker,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ) {
        $this->speaker   = $speaker;
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function getSpeaker() : EventSpeaker
    {
        return $this->speaker;
    }

    public function getStartDate() : DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate() : DateTimeImmutable
    {
        return $this->endDate;
    }
}
