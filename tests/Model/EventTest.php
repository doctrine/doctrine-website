<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Model;

use DateTimeImmutable;
use Doctrine\Website\Model\Event;
use Doctrine\Website\Model\EventLocation;
use Doctrine\Website\Model\EventType;
use Doctrine\Website\Tests\TestCase;
use function array_merge;

final class EventTest extends TestCase
{
    public function testIsWebinar() : void
    {
        self::assertTrue($this->createTestEvent()->isWebinar());
        self::assertFalse($this->createTestEvent([
            'type' => EventType::CONFERENCE,
            'location' => [],
        ])->isWebinar());
    }

    public function testIsConference() : void
    {
        self::assertFalse($this->createTestEvent()->isConference());
        self::assertTrue($this->createTestEvent([
            'type' => EventType::CONFERENCE,
            'location' => [],
        ])->isConference());
    }

    public function testGetSkuProd() : void
    {
        self::assertSame('prod_123', $this->createTestEvent(['env' => 'prod'])->getSku());
    }

    public function testGetSkuTest() : void
    {
        self::assertSame('test_123', $this->createTestEvent()->getSku());
    }

    public function testGetName() : void
    {
        self::assertSame('Doctrine for Beginners', $this->createTestEvent()->getName());
    }

    public function testGetSlug() : void
    {
        self::assertSame('doctrine-for-beginners', $this->createTestEvent()->getSlug());
    }

    public function testGetJoinUrl() : void
    {
        self::assertSame('https://www.joinurl.com', $this->createTestEvent()->getJoinUrl());
    }

    public function testGetDates() : void
    {
        self::assertEquals(
            new DateTimeImmutable('2019-05-28 11:00:00'),
            $this->createTestEvent()->getDates()->getStart()
        );

        self::assertEquals(
            new DateTimeImmutable('2019-05-28 11:00:00'),
            $this->createTestEvent()->getStartDate()
        );

        self::assertEquals(
            new DateTimeImmutable('2019-05-28 11:45:00'),
            $this->createTestEvent()->getDates()->getEnd()
        );

        self::assertEquals(
            new DateTimeImmutable('2019-05-28 11:45:00'),
            $this->createTestEvent()->getEndDate()
        );

        self::assertSame(0, $this->createTestEvent()->getDates()->getNumDays());
        self::assertSame(0, $this->createTestEvent()->getDates()->getNumHours());
        self::assertSame(45, $this->createTestEvent()->getDates()->getNumMinutes());
        self::assertSame('45-minute', $this->createTestEvent()->getDates()->getDuration());

        self::assertSame(3, $this->createTestEvent([
            'schedule' => [],
            'startDate' => '2019-05-28',
            'endDate' => '2019-05-30',
        ])->getDates()->getNumDays());

        self::assertSame('3-day', $this->createTestEvent([
            'schedule' => [],
            'startDate' => '2019-05-28',
            'endDate' => '2019-05-30',
        ])->getDates()->getDuration());

        self::assertSame(71, $this->createTestEvent([
            'schedule' => [],
            'startDate' => '2019-05-28 11:00:00',
            'endDate' => '2019-05-30 10:00:00',
        ])->getDates()->getNumHours());
    }

    public function testGetRegistrationDates() : void
    {
        self::assertEquals(
            new DateTimeImmutable('2019-05-01'),
            $this->createTestEvent()->getRegistrationDates()->getStart()
        );

        self::assertEquals(
            new DateTimeImmutable('2019-05-27'),
            $this->createTestEvent()->getRegistrationDates()->getEnd()
        );
    }

    public function testGetCfp() : void
    {
        self::assertTrue($this->createTestEvent()->getCfp()->exists());

        self::assertSame(
            'https://docs.google.com/forms/d/e/123/viewform',
            $this->createTestEvent()->getCfp()->getGoogleFormUrl()
        );

        self::assertSame(
            'https://docs.google.com/forms/d/e/123/viewform?embedded=true',
            $this->createTestEvent()->getCfp()->getEmbeddedGoogleFormUrl()
        );

        self::assertEquals(
            new DateTimeImmutable('2019-05-01'),
            $this->createTestEvent()->getCfp()->getDates()->getStart()
        );

        self::assertEquals(
            new DateTimeImmutable('2019-05-02'),
            $this->createTestEvent()->getCfp()->getDates()->getEnd()
        );
    }

    public function testGetLocation() : void
    {
        self::assertNull($this->createTestEvent()->getLocation());

        $event = $this->createTestEvent([
            'type' => EventType::CONFERENCE,
            'location' => [
                'name' => 'Awesome Hotel',
                'address' => [
                    'line1' => 'Line 1',
                    'line2' => 'Line 2',
                    'city' => 'City',
                    'state' => 'State',
                    'zipCode' => 'Zip Code',
                    'countryCode' => 'Country Code',
                ],
            ],
        ]);

        /** @var EventLocation $location */
        $location = $event->getLocation();

        self::assertSame('Awesome Hotel', $location->getName());
        self::assertSame('Line 1', $location->getAddress()->getLine1());
        self::assertSame('Line 2', $location->getAddress()->getLine2());
        self::assertSame('City', $location->getAddress()->getCity());
        self::assertSame('State', $location->getAddress()->getState());
        self::assertSame('Zip Code', $location->getAddress()->getZipCode());
        self::assertSame('Country Code', $location->getAddress()->getCountryCode());
    }

    public function testGetSponsors() : void
    {
        $sponsor = $this->createTestEvent()->getSponsors()->first();

        self::assertSame('Blackfire.io', $sponsor->getName());
        self::assertSame('https://blackfire.io/', $sponsor->getUrl());
        self::assertSame(
            'https://blackfire.io/?utm_source=doctrine&utm_medium=website&utm_campaign=doctrine-for-beginners',
            $sponsor->getUrlWithUtmParameters()
        );
        self::assertSame('/images/blackfire.svg', $sponsor->getLogo());
    }

    public function testGetSpeakers() : void
    {
        $speaker = $this->createTestEvent()->getSpeakers()->first();

        self::assertSame('Jonathan H. Wage', $speaker->getName());
        self::assertSame('Doctrine for Beginners', $speaker->getTopic());
        self::assertSame('doctrine-for-beginners', $speaker->getTopicSlug());
        self::assertSame('Come to this talk prepared to learn about the Doctrine PHP open source project. The Doctrine project has been around for over a decade and has evolved from database abstraction software that dates back to the PEAR days. The packages provided by the Doctrine project have been downloaded almost a billion times from packagist. In this talk we will take you through how to get started with Doctrine and how to take advantage of some of the more advanced features.', $speaker->getDescription());
    }

    public function testGetSchedule() : void
    {
        $event = $this->createTestEvent();

        $speaker = $event->getSpeakers()->first();
        $slot    = $event->getSchedule()->first();

        self::assertSame($speaker, $slot->getSpeaker());
    }

    public function testGetDescription() : void
    {
        self::assertSame('Test Description', $this->createTestEvent()->getDescription());
    }

    public function testGetPrice() : void
    {
        self::assertSame(0.00, $this->createTestEvent()->getPrice());
        self::assertSame(5.00, $this->createTestEvent(['price' => 5.00])->getPrice());
    }

    public function testIsFree() : void
    {
        self::assertTrue($this->createTestEvent()->isFree());
        self::assertFalse($this->createTestEvent(['price' => 5.00])->isFree());
    }

    /**
     * @param mixed[] $data
     */
    private function createTestEvent(array $data = []) : Event
    {
        return $this->createEvent(array_merge([
            'env' => 'dev',
            'sku' => [
                'test' => 'test_123',
                'prod' => 'prod_123',
            ],
            'cfp' => [
                'googleFormId' => '123',
                'startDate' => '2019-05-01',
                'endDate' => '2019-05-02',
            ],
            'name' => 'Doctrine for Beginners',
            'slug' => 'doctrine-for-beginners',
            'description' => 'Test Description',
            'price' => 0.0,
            'joinUrl' => 'https://www.joinurl.com',
            'startDate' => '2019-05-28',
            'endDate' => '2019-05-28',
            'registrationStartDate' => '2019-05-01',
            'registrationEndDate' => '2019-05-27',
            'sponsors' => [
                [
                    'name' => 'Blackfire.io',
                    'url' => 'https://blackfire.io/',
                    'logo' => '/images/blackfire.svg',
                    'utmParameters' => ['utm_source' => 'doctrine'],
                ],
            ],
            'speakers' => [
                [
                    'name' => 'jwage',
                    'topic' => 'Doctrine for Beginners',
                    'topicSlug' => 'doctrine-for-beginners',
                    'description' => 'Come to this talk prepared to learn about the Doctrine PHP open source project. The Doctrine project has been around for over a decade and has evolved from database abstraction software that dates back to the PEAR days. The packages provided by the Doctrine project have been downloaded almost a billion times from packagist. In this talk we will take you through how to get started with Doctrine and how to take advantage of some of the more advanced features.',
                ],
            ],
            'schedule' => [
                [
                    'topicSlug' => 'doctrine-for-beginners',
                    'startDate' => '2019-05-28 11:00',
                    'endDate' => '2019-05-28 11:45',
                ],
            ],
        ], $data));
    }
}
