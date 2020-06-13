<?php

declare(strict_types=1);

namespace Doctrine\Website\Commands;

use Doctrine\ORM\EntityManager;
use Doctrine\Website\Event\EmailParticipants;
use Doctrine\Website\Event\GetStripeEventParticipants;
use Doctrine\Website\Model\Entity\EventParticipant;
use Doctrine\Website\Model\Entity\EventParticipantRepository;
use Doctrine\Website\Model\Event;
use Doctrine\Website\Repositories\EventRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function array_filter;
use function array_map;
use function assert;
use function count;
use function in_array;
use function is_bool;
use function sprintf;

class EventParticipantsCommand extends Command
{
    /** @var EventRepository */
    private $eventRepository;

    /** @var EventParticipantRepository */
    private $eventParticipantRepository;

    /** @var GetStripeEventParticipants */
    private $getStripeEventParticipants;

    /** @var EmailParticipants */
    private $emailParticipants;

    /** @var EntityManager */
    private $entityManager;

    public function __construct(
        EventRepository $eventRepository,
        EventParticipantRepository $eventParticipantRepository,
        GetStripeEventParticipants $getStripeEventParticipants,
        EmailParticipants $emailParticipants,
        EntityManager $entityManager
    ) {
        $this->eventRepository            = $eventRepository;
        $this->eventParticipantRepository = $eventParticipantRepository;
        $this->getStripeEventParticipants = $getStripeEventParticipants;
        $this->emailParticipants          = $emailParticipants;
        $this->entityManager              = $entityManager;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('event-participants')
            ->setDescription('Command to check for event participants using the Stripe API.')
            ->addOption(
                'save',
                null,
                InputOption::VALUE_NONE,
                'Save new participants that are found.'
            )
            ->addOption(
                'email',
                null,
                InputOption::VALUE_NONE,
                'E-Mail new participants that are found.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);

        $events = $this->eventRepository->findUpcomingEvents();

        foreach ($events as $event) {
            $io->title(sprintf('%s Participants', $event->getName()));

            $eventParticipants = $this->getStripeEventParticipants->__invoke($event);

            if ($eventParticipants === []) {
                $io->warning(sprintf('No participants found for "%s". Get out there and market!', $event->getName()));

                continue;
            }

            $newEventParticipants = $this->getNewEventParticipants($eventParticipants);

            $save = $input->getOption('save');
            assert(is_bool($save));

            if ($save) {
                $this->saveEventParticipants($newEventParticipants);
            }

            $email = $input->getOption('email');
            assert(is_bool($email));

            if ($email) {
                $this->emailEventParticipants($io, $event, $newEventParticipants);
            }

            $header = ['E-Mail', 'Quantity', 'New'];

            $rows = $this->createEventParticipantsTableRows(
                $eventParticipants,
                $newEventParticipants
            );

            $io->table($header, $rows);
        }

        return 0;
    }


    /**
     * @param EventParticipant[] $eventParticipants
     * @param EventParticipant[] $newEventParticipants
     *
     * @return mixed[][]
     */
    private function createEventParticipantsTableRows(
        array $eventParticipants,
        array $newEventParticipants
    ) : array {
        return array_map(
            static function (EventParticipant $participant) use ($newEventParticipants) : array {
                $isNew = in_array($participant, $newEventParticipants, true) ? 'Yes' : 'No';

                return [$participant->getEmail(), $participant->getQuantity(), $isNew];
            },
            $eventParticipants
        );
    }

    /**
     * @param EventParticipant[] $eventParticipants
     *
     * @return EventParticipant[]
     */
    private function getNewEventParticipants(array $eventParticipants) : array
    {
        return array_filter($eventParticipants, function (EventParticipant $eventParticipant) : bool {
            return $this->eventParticipantRepository
                ->findOneByEmail($eventParticipant->getEmail()) === null;
        });
    }

    /**
     * @param EventParticipant[] $eventParticipants
     */
    private function saveEventParticipants(array $eventParticipants) : void
    {
        foreach ($eventParticipants as $eventParticipant) {
            $this->entityManager->persist($eventParticipant);
        }

        $this->entityManager->flush();
    }

    /**
     * @param EventParticipant[] $eventParticipants
     */
    private function emailEventParticipants(
        SymfonyStyle $io,
        Event $event,
        array $eventParticipants
    ) : void {
        $this->emailParticipants->__invoke($event, $eventParticipants);

        $io->text(sprintf('E-mailed <info>%d</info> participants.', count($eventParticipants)));
    }
}
