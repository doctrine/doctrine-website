<?php

declare(strict_types=1);

namespace Doctrine\Website\Model\Entity;

use Doctrine\ORM\EntityRepository;

use function assert;

final class EventParticipantRepository extends EntityRepository
{
    public function findOneByEmail(string $email): ?EventParticipant
    {
        $eventParticipant = $this->findOneBy(['email' => $email]);
        assert($eventParticipant instanceof EventParticipant);

        return $eventParticipant;
    }

    /**
     * @return EventParticipant[]
     */
    public function findByEventId(int $eventId): array
    {
        /** @var EventParticipant[] $eventParticipants */
        $eventParticipants = $this->findBy(['eventId' => $eventId]);

        return $eventParticipants;
    }
}
