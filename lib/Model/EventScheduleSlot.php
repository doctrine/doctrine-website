<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;

final class EventScheduleSlot
{
    public function __construct(
        private EventSpeaker $speaker,
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate,
    ) {
    }

    public function getSpeaker(): EventSpeaker
    {
        return $this->speaker;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }
}
