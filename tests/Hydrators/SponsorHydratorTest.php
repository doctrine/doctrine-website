<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use Doctrine\Website\Hydrators\SponsorHydrator;
use Doctrine\Website\Model\Sponsor;
use Doctrine\Website\Model\UtmParameters;

class SponsorHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator       = $this->createHydrator(SponsorHydrator::class);
        $propertyValues = [
            'name' => 'name',
            'url' => 'url',
            'highlighted' => true,
            'utmParameters' => [
                'utm_source'  => 'utm_source',
                'utm_medium'   => 'utm_medium',
                'utm_campaign' => 'utm_campaign',
            ],
        ];

        $expected = new Sponsor();
        $this->populate($expected, [
            'name' => 'name',
            'url' => 'url',
            'highlighted' => true,
            'utmParameters' => new UtmParameters([
                'utm_source'  => 'utm_source',
                'utm_medium'   => 'utm_medium',
                'utm_campaign' => 'utm_campaign',
            ]),
        ]);

        $sponsor = new Sponsor();

        $hydrator->hydrate($sponsor, $propertyValues);

        self::assertEquals($expected, $sponsor);
    }
}
