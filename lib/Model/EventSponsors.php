<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use function array_merge;

final class EventSponsors extends AbstractLazyCollection
{
    /** @var mixed[] */
    private $event;

    /**
     * @param mixed[] $event
     */
    public function __construct(array $event)
    {
        $this->event = $event;
    }

    protected function doInitialize() : void
    {
        $sponsors = [];

        foreach ($this->event['sponsors'] ?? [] as $sponsor) {
            $sponsors[] = new EventSponsor(
                (string) ($sponsor['name'] ?? ''),
                (string) ($sponsor['url'] ?? ''),
                (string) ($sponsor['logo'] ?? ''),
                new UtmParameters(
                    array_merge(
                        [
                            'utm_source'  => 'doctrine',
                            'utm_medium'   => 'website',
                            'utm_campaign' => $this->event['slug'],
                        ],
                        $sponsor['utmParameters'] ?? []
                    )
                )
            );
        }

        $this->collection = new ArrayCollection($sponsors);
    }
}
