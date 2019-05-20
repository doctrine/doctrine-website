<?php

declare(strict_types=1);

namespace Doctrine\Website\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\StaticWebsiteGenerator\Request\RequestCollection;
use Doctrine\Website\Model\Event;
use Doctrine\Website\Repositories\EventRepository;

class EventRequests
{
    /** @var EventRepository */
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getEvents() : RequestCollection
    {
        /** @var Event[] $events */
        $events = $this->eventRepository->findAll();

        $requests = [];

        foreach ($events as $event) {
            $requests[] = [
                'id'   => $event->getId(),
                'slug' => $event->getSlug(),
            ];
        }

        return new ArrayRequestCollection($requests);
    }
}
