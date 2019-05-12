<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use Doctrine\Website\Model\Sponsor;
use Doctrine\Website\Model\UtmParameters;
use function array_merge;

/**
 * @property string $name
 * @property string $url
 * @property UtmParameters $utmParameters
 * @property bool $highlighted
 */
final class SponsorHydrator extends ModelHydrator
{
    protected function getClassName() : string
    {
        return Sponsor::class;
    }

    /**
     * @param mixed[] $data
     */
    protected function doHydrate(array $data) : void
    {
        $this->name = (string) ($data['name'] ?? '');
        $this->url  = (string) ($data['url'] ?? '');

        $this->utmParameters = new UtmParameters(
            array_merge(
                [
                    'utm_source'  => 'doctrine',
                    'utm_medium'   => 'website',
                    'utm_campaign' => 'sponsors',
                ],
                $data['utmParameters'] ?? []
            )
        );

        $this->highlighted = (bool) ($data['highlighted'] ?? '');
    }
}
