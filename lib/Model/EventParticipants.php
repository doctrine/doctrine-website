<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Website\Model\Entity\EventParticipant;
use Doctrine\Website\Model\Entity\EventParticipantRepository;

/** @template-extends AbstractLazyCollection<int, EventParticipant> */
final class EventParticipants extends AbstractLazyCollection
{
    public function __construct(private int $eventId, private EventParticipantRepository $eventParticipantRepository)
    {
    }

    protected function doInitialize(): void
    {
        /** @var EventParticipant[] $eventParticipants */
        $eventParticipants = $this->eventParticipantRepository
            ->findByEventId($this->eventId);

        $this->collection = new ArrayCollection($eventParticipants);
    }
}
