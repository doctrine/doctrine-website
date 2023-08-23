<?php

declare(strict_types=1);

namespace Doctrine\Website\Event;

use Doctrine\Website\Email\SendEmail;
use Doctrine\Website\Model\Entity\EventParticipant;
use Doctrine\Website\Model\Event;

final class EmailParticipants
{
    public function __construct(private SendEmail $sendEmail)
    {
    }

    /** @param EventParticipant[] $participants */
    public function __invoke(Event $event, array $participants): void
    {
        foreach ($participants as $participant) {
            $this->sendEmail->__invoke(
                $participant->getEmail(),
                'emails/events/participant-ticket.html.twig',
                [
                    'event' => $event,
                    'participant' => $participant,
                ],
            );
        }
    }
}
