<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

use function sprintf;

/** @template-extends AbstractLazyCollection<int, EventScheduleSlot> */
final class EventSchedule extends AbstractLazyCollection
{
    /** @param mixed[] $event */
    public function __construct(private array $event, private EventSpeakers $speakers)
    {
    }

    protected function doInitialize(): void
    {
        $slots = [];

        foreach ($this->event['schedule'] as $slot) {
            if (! isset($this->speakers[$slot['topicSlug']])) {
                throw new InvalidArgumentException(sprintf(
                    'Could not find speaker with topicSlug "%s".',
                    $slot['topicSlug'],
                ));
            }

            $eventSpeaker = $this->speakers[$slot['topicSlug']];

            $slots[] = new EventScheduleSlot(
                $eventSpeaker,
                new DateTimeImmutable($slot['startDate'] ?? ''),
                new DateTimeImmutable($slot['endDate'] ?? ''),
            );
        }

        $this->collection = new ArrayCollection($slots);
    }
}
