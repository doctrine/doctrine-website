<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use Doctrine\Website\Model\Partner;
use Doctrine\Website\Model\PartnerDetails;
use Doctrine\Website\Model\UtmParameters;

use function array_merge;

/**
 * @property string $name
 * @property string $slug
 * @property string $url
 * @property string $logo
 * @property string $bio
 * @property bool $featured
 * @property PartnerDetails $details
 * @property UtmParameters $utmParameters
 * @template-extends ModelHydrator<Partner>
 */
final class PartnerHydrator extends ModelHydrator
{
    /** @return class-string<Partner> */
    protected function getClassName(): string
    {
        return Partner::class;
    }

    /** @param mixed[] $data */
    protected function doHydrate(array $data): void
    {
        $this->name     = (string) ($data['name'] ?? '');
        $this->slug     = (string) ($data['slug'] ?? '');
        $this->url      = (string) ($data['url'] ?? '');
        $this->logo     = (string) ($data['logo'] ?? '');
        $this->bio      = (string) ($data['bio'] ?? '');
        $this->featured = (bool) ($data['featured'] ?? false);

        $this->details = new PartnerDetails(
            (string) ($data['details']['label'] ?? ''),
            $data['details']['items'] ?? [],
        );

        $this->utmParameters = new UtmParameters(
            array_merge(
                [
                    'utm_source'  => 'doctrine',
                    'utm_medium'   => 'website',
                    'utm_campaign' => 'partners',
                ],
                $data['utmParameters'] ?? [],
            ),
        );
    }
}
