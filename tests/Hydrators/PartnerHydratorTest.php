<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use Doctrine\Website\Hydrators\PartnerHydrator;
use Doctrine\Website\Model\Partner;
use Doctrine\Website\Model\PartnerDetails;
use Doctrine\Website\Model\UtmParameters;

class PartnerHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator       = $this->createHydrator(PartnerHydrator::class);
        $propertyValues = [
            'name' => 'name',
            'url' => 'url',
            'slug' => 'slug',
            'logo' => 'logo',
            'bio' => 'bio',
            'featured' => true,
            'details' => [
                'label' => 'label',
                'items' => ['item'],
            ],
            'utmParameters' => [
                'utm_source'  => 'utm_source',
                'utm_medium'   => 'utm_medium',
                'utm_campaign' => 'utm_campaign',
            ],
        ];

        $expected = new Partner();
        $this->populate($expected, [
            'name' => 'name',
            'url' => 'url',
            'slug' => 'slug',
            'logo' => 'logo',
            'bio' => 'bio',
            'featured' => true,
            'details' => new PartnerDetails('label', ['item']),
            'utmParameters' => new UtmParameters([
                'utm_source'  => 'utm_source',
                'utm_medium'   => 'utm_medium',
                'utm_campaign' => 'utm_campaign',
            ]),
        ]);

        $doctrineUser = new Partner();

        $hydrator->hydrate($doctrineUser, $propertyValues);

        self::assertEquals($expected, $doctrineUser);
    }

    public function testHydrateDefaultValues(): void
    {
        $hydrator = $this->createHydrator(PartnerHydrator::class);
        $expected = new Partner();
        $this->populate($expected, [
            'name' => '',
            'url' => '',
            'slug' => '',
            'logo' => '',
            'bio' => '',
            'featured' => false,
            'details' => new PartnerDetails('', []),
            'utmParameters' => new UtmParameters([
                'utm_source'  => 'doctrine',
                'utm_medium'   => 'website',
                'utm_campaign' => 'partners',
            ]),
        ]);

        $doctrineUser = new Partner();

        $hydrator->hydrate($doctrineUser, []);

        self::assertEquals($expected, $doctrineUser);
    }
}
