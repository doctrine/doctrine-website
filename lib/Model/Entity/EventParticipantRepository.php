<?php

declare(strict_types=1);

namespace Doctrine\Website\Model\Entity;

/** @template T of EventParticipant */
final class EventParticipantRepository
{
    public function findOneByEmail(string $email): EventParticipant|null
    {
        return null;
    }

    /** @return EventParticipant[] */
    public function findByEventId(int $eventId): array
    {
        return [];
    }
}
