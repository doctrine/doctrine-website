<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\ObjectManagerInterface;
use Doctrine\Website\Application;
use Doctrine\Website\Model\Address;
use Doctrine\Website\Model\DateTimeRange;
use Doctrine\Website\Model\Event;
use Doctrine\Website\Model\EventCfp;
use Doctrine\Website\Model\EventLocation;
use Doctrine\Website\Model\EventParticipants;
use Doctrine\Website\Model\EventSchedule;
use Doctrine\Website\Model\EventSpeakers;
use Doctrine\Website\Model\EventSponsors;
use Doctrine\Website\Model\EventType;
use InvalidArgumentException;

use function current;
use function end;
use function sprintf;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property EventLocation|null $location
 * @property string $sku
 * @property string $joinUrl
 * @property EventCfp $cfp
 * @property EventSponsors $sponsors
 * @property EventSpeakers $speakers
 * @property EventSchedule $schedule
 * @property EventParticipants $participants
 * @property DateTimeRange $dateTimeRange
 * @property DateTimeRange $registrationDateTimeRange
 * @property string $description
 * @property float $price
 * @template-extends ModelHydrator<Event>
 */
final class EventHydrator extends ModelHydrator
{
    private const ENV_SKU_MAP = [
        'dev'                    => 'test',
        Application::ENV_PROD    => Application::ENV_PROD,
        Application::ENV_STAGING => 'test',
        'test'                   => 'test',
    ];

    public function __construct(
        ObjectManagerInterface $objectManager,
        private string $env,
    ) {
        parent::__construct($objectManager);
    }

    /** @return class-string<Event> */
    protected function getClassName(): string
    {
        return Event::class;
    }

    /** @param mixed[] $data */
    protected function doHydrate(array $data): void
    {
        $this->id   = (int) ($data['id'] ?? 0);
        $this->type = (string) ($data['type'] ?? EventType::WEBINAR);

        if ($this->type === EventType::CONFERENCE) {
            if (! isset($data['location'])) {
                throw new InvalidArgumentException(
                    sprintf('Event type of "%s" must provide a "location" field.', $this->type),
                );
            }

            $this->location = new EventLocation(
                (string) ($data['location']['name'] ?? ''),
                new Address(
                    (string) ($data['location']['address']['line1'] ?? ''),
                    (string) ($data['location']['address']['line2'] ?? ''),
                    (string) ($data['location']['address']['city'] ?? ''),
                    (string) ($data['location']['address']['state'] ?? ''),
                    (string) ($data['location']['address']['zipCode'] ?? ''),
                    (string) ($data['location']['address']['countryCode'] ?? ''),
                ),
            );
        }

        if (isset($data['sku'])) {
            if (! isset(self::ENV_SKU_MAP[$this->env])) {
                throw new InvalidArgumentException(sprintf('Invalid env "%s".', $this->env));
            }

            $skuKey = self::ENV_SKU_MAP[$this->env];

            if (! isset($data['sku'][$skuKey])) {
                throw new InvalidArgumentException(
                    sprintf('Sku key with "%s" does not exist.', $skuKey),
                );
            }

            $this->sku = (string) ($data['sku'][$skuKey] ?? '');
        } else {
            $this->sku = '';
        }

        $this->name    = (string) ($data['name'] ?? '');
        $this->slug    = (string) ($data['slug'] ?? '');
        $this->joinUrl = (string) ($data['joinUrl'] ?? '');

        $this->cfp = new EventCfp(
            (string) ($data['cfp']['googleFormId'] ?? ''),
            new DateTimeRange(
                new DateTimeImmutable($data['cfp']['startDate'] ?? ''),
                new DateTimeImmutable($data['cfp']['endDate'] ?? ''),
            ),
        );

        $this->sponsors = new EventSponsors($data);
        $this->speakers = new EventSpeakers($data, $this->objectManager);
        $this->schedule = new EventSchedule($data, $this->speakers);

        if ($data['schedule'] !== []) {
            $firstSlot = current($data['schedule']);
            $lastSlot  = end($data['schedule']);

            $this->dateTimeRange = new DateTimeRange(
                new DateTimeImmutable($firstSlot['startDate'] ?? ''),
                new DateTimeImmutable($lastSlot['endDate'] ?? ''),
            );
        } else {
            $this->dateTimeRange = new DateTimeRange(
                new DateTimeImmutable($data['startDate'] ?? ''),
                new DateTimeImmutable($data['endDate'] ?? ''),
            );
        }

        $this->registrationDateTimeRange = new DateTimeRange(
            isset($data['registrationStartDate'])
                ? new DateTimeImmutable($data['registrationStartDate'])
                : $this->dateTimeRange->getStart(),
            isset($data['registrationEndDate'])
                ? new DateTimeImmutable($data['registrationEndDate'])
                : $this->dateTimeRange->getEnd(),
        );

        $this->description = (string) ($data['description'] ?? '');

        $this->price = (float) ($data['price'] ?? 0.00);

        $this->participants = new EventParticipants();
    }
}
