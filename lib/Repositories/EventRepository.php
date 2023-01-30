<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\Event;
use InvalidArgumentException;

use function sprintf;

/**
 * @template T of Event
 * @template-extends BasicObjectRepository<T>
 */
class EventRepository extends BasicObjectRepository
{
    public function findOneById(int $id): Event
    {
        $event = $this->findOneBy(['id' => $id]);

        if ($event === null) {
            throw new InvalidArgumentException(sprintf('Could not find Event with id "%s"', $id));
        }

        return $event;
    }

    /** @return Event[] */
    public function findUpcomingEvents(): array
    {
        /** @var Event[] $events */
        $events = $this->findBy([], ['startDate' => 'asc']);

        $criteria = Criteria::create()
            ->where(Criteria::expr()->gt('startDate', new DateTimeImmutable()));

        return (new ArrayCollection($events))->matching($criteria)->toArray();
    }

    /** @return Event[] */
    public function findPastEvents(): array
    {
        /** @var Event[] $events */
        $events = $this->findBy([], ['endDate' => 'desc']);

        $criteria = Criteria::create()
            ->where(Criteria::expr()->lt('endDate', new DateTimeImmutable()));

        return (new ArrayCollection($events))->matching($criteria)->toArray();
    }
}
