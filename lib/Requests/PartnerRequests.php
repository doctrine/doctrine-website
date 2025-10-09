<?php

declare(strict_types=1);

namespace Doctrine\Website\Requests;

use Doctrine\Website\Model\Partner;
use Doctrine\Website\Repositories\PartnerRepository;
use Doctrine\Website\StaticGenerator\Request\ArrayRequestCollection;
use Doctrine\Website\StaticGenerator\Request\RequestCollection;

final readonly class PartnerRequests
{
    /** @param PartnerRepository<Partner> $partnerRepository */
    public function __construct(
        private PartnerRepository $partnerRepository,
    ) {
    }

    public function getPartners(): RequestCollection
    {
        /** @var Partner[] $partners */
        $partners = $this->partnerRepository->findAll();

        $requests = [];

        foreach ($partners as $partner) {
            $requests[] = [
                'slug' => $partner->getSlug(),
            ];
        }

        return new ArrayRequestCollection($requests);
    }
}
