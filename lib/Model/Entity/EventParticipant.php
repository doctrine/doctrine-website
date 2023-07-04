<?php

declare(strict_types=1);

namespace Doctrine\Website\Model\Entity;

use Doctrine\Website\Model\Event;

/**
 * @Entity(repositoryClass="EventParticipantRepository")
 * @Table(name="event_participants")
 */
final class EventParticipant
{
    /**
     * @var int|null
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     **/
    private $id;

    /**
     * @var string
     * @Column(type="string")
     **/
    private $email;

    /**
     * @var int
     * @Column(type="integer")
     **/
    private $quantity;

    /**
     * @var int
     * @Column(type="integer")
     **/
    private $eventId;

    public function __construct(Event $event, string $email, int $quantity)
    {
        $this->eventId  = $event->getId();
        $this->email    = $email;
        $this->quantity = $quantity;
    }

    public function getId(): ?int
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
