<?php

declare(strict_types=1);

namespace Doctrine\Website\Model\Entity;

use Doctrine\ORM\EntityRepository;

final class EventParticipantRepository extends EntityRepository
{
    public function findOneByEmail(string $email) : ?EventParticipant
    {
        /** @var EventParticipant $eventParticipant */
        $eventParticipant = $this->findOneBy(['email' => $email]);

        return $eventParticipant;
    }

    /**
     * @return EventParticipant[]
     */
    public function findByEventId(int $eventId) : array
    {
        /** @var EventParticipant[] $eventParticipants */
        $eventParticipants = $this->findBy(['eventId' => $eventId]);

        return $eventParticipants;
    }
}
