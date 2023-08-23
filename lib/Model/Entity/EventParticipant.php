<?php

declare(strict_types=1);

namespace Doctrine\Website\Model\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Website\Model\Event;

#[Entity(repositoryClass: EventParticipantRepository::class)]
#[Table(name: 'event_participants')]
final class EventParticipant
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int|null $id = null;

    #[Column(type: 'string')]
    private string $email;

    #[Column(type: 'integer')]
    private int $quantity;

    #[Column(type: 'integer')]
    private int $eventId;

    public function __construct(Event $event, string $email, int $quantity)
    {
        $this->eventId  = $event->getId();
        $this->email    = $email;
        $this->quantity = $quantity;
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
