<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;
use Doctrine\Website\Repositories\EventRepository;

final class EventsController
{
    /** @var EventRepository */
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function index() : Response
    {
        $upcomingEvents = $this->eventRepository->findUpcomingEvents();
        $pastEvents     = $this->eventRepository->findPastEvents();

        return new Response([
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
        ]);
    }

    public function view(string $id, string $slug) : Response
    {
        $event = $this->eventRepository->findOneById((int) $id);

        return new Response(['event' => $event], '/event.html.twig');
    }

    public function cfp(string $id, string $slug) : Response
    {
        $event = $this->eventRepository->findOneById((int) $id);

        return new Response(['event' => $event], '/event-cfp.html.twig');
    }

    public function suggest() : Response
    {
        return new Response([]);
    }
}
