<?php

declare(strict_types=1);

namespace Doctrine\Website\Event;

use Doctrine\Website\Model\Entity\EventParticipant;
use Doctrine\Website\Model\Event;
use Stripe;

use function array_map;
use function array_merge;
use function array_values;
use function end;
use function iterator_to_array;
use function strtotime;

final class GetStripeEventParticipants
{
    /** @return EventParticipant[] */
    public function __invoke(Event $event): array
    {
        $stripeCheckouts = $this->getAllEventStripeCheckouts($event);

        $participants = [];

        $customers = [];

        foreach ($stripeCheckouts as $stripeCheckout) {
            $item       = $stripeCheckout['data']['object']['display_items'][0];
            $sku        = $item['sku']['id'];
            $quantity   = $item['quantity'];
            $customerId = $stripeCheckout['data']['object']['customer'];

            if ($sku !== $event->getSku()) {
                continue;
            }

            if (! isset($customers[$customerId])) {
                $customers[$customerId] = Stripe\Customer::retrieve($customerId);
            }

            $customer = $customers[$customerId];

            if (! isset($participants[$customer['email']])) {
                $participants[$customer['email']] = [
                    'email' => $customer['email'],
                    'quantity' => $quantity,
                ];
            } else {
                $participants[$customer['email']]['quantity'] += $quantity;
            }
        }

        return array_map(static function (array $participant) use ($event): EventParticipant {
            return new EventParticipant(
                $event,
                $participant['email'],
                $participant['quantity'],
            );
        }, array_values($participants));
    }

    /** @return mixed[][] */
    private function getAllEventStripeCheckouts(Event $event): array
    {
        $allEventStripeCheckouts = [];
        $startingAfter           = null;

        while (true) {
            $eventStripeCheckouts = $this->getEventStripeCheckouts($event, $startingAfter);

            $eventStripeCheckoutsArray = iterator_to_array($eventStripeCheckouts);

            $allEventStripeCheckouts = array_merge(
                $allEventStripeCheckouts,
                $eventStripeCheckoutsArray,
            );

            if ($eventStripeCheckouts['has_more'] === false) {
                break;
            }

            $startingAfter = end($eventStripeCheckoutsArray)['id'];
        }

        return $allEventStripeCheckouts;
    }

    /** @return Stripe\Collection<string, mixed> */
    private function getEventStripeCheckouts(
        Event $event,
        ?string $startingAfter = null
    ): Stripe\Collection {
        $parameters = [
            'created' => ['gt' => strtotime('1 year ago')],
            'limit' => 100,
            'type' => 'checkout.session.completed',
        ];

        if ($startingAfter !== null) {
            $parameters['starting_after'] = $startingAfter;
        }

        return Stripe\Event::all($parameters);
    }
}
