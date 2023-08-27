<?php

declare(strict_types=1);

namespace Doctrine\Website\Model\Entity;

use Doctrine\Website\Model\Event;

final class EventParticipant
{
    private int|null $id = null;

    private int $eventId;

    public function __construct(Event $event, private string $email, private int $quantity)
    {
        $this->eventId = $event->getId();
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }
}
