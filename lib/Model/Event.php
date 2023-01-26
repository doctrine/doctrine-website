<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\Mapping\LoadMetadataInterface;

final class Event implements LoadMetadataInterface
{
    private int $id;

    private string $type;

    private string $sku;

    private string $name;

    private string $slug;

    private string $joinUrl;

    private DateTimeRange $dateTimeRange;

    private DateTimeRange $registrationDateTimeRange;

    private EventCfp $cfp;

    private EventLocation|null $location = null;

    private EventSponsors $sponsors;

    private EventSpeakers $speakers;

    private EventSchedule $schedule;

    private EventParticipants $participants;

    private string $description;

    private float $price;

    public static function loadMetadata(ClassMetadataInterface $metadata): void
    {
        $metadata->setIdentifier(['id']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isWebinar(): bool
    {
        return $this->type === EventType::WEBINAR;
    }

    public function isConference(): bool
    {
        return $this->type === EventType::CONFERENCE;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getJoinUrl(): string
    {
        return $this->joinUrl;
    }

    public function getDates(): DateTimeRange
    {
        return $this->dateTimeRange;
    }

    public function getRegistrationDates(): DateTimeRange
    {
        return $this->registrationDateTimeRange;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->dateTimeRange->getStart();
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->dateTimeRange->getEnd();
    }

    public function getCfp(): EventCfp
    {
        return $this->cfp;
    }

    public function getLocation(): EventLocation|null
    {
        return $this->location;
    }

    public function getSponsors(): EventSponsors
    {
        return $this->sponsors;
    }

    public function getSpeakers(): EventSpeakers
    {
        return $this->speakers;
    }

    public function getSchedule(): EventSchedule
    {
        return $this->schedule;
    }

    public function getParticipants(): EventParticipants
    {
        return $this->participants;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function isFree(): bool
    {
        return $this->price === 0.00;
    }
}
