<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Website\Model\Entity\EventParticipantRepository;

final class EventParticipants extends AbstractLazyCollection
{
    /** @var int */
    private $eventId;

    /** @var EventParticipantRepository */
    private $eventParticipantRepository;

    public function __construct(int $eventId, EventParticipantRepository $eventParticipantRepository)
    {
        $this->eventId                    = $eventId;
        $this->eventParticipantRepository = $eventParticipantRepository;
    }

    protected function doInitialize() : void
    {
        $eventParticipants = $this->eventParticipantRepository
            ->findByEventId($this->eventId);

        $this->collection = new ArrayCollection($eventParticipants);
    }
}
