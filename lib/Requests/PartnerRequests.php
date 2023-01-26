<?php

declare(strict_types=1);

namespace Doctrine\Website\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\StaticWebsiteGenerator\Request\RequestCollection;
use Doctrine\Website\Model\Partner;
use Doctrine\Website\Repositories\PartnerRepository;

class PartnerRequests
{
    public function __construct(private PartnerRepository $partnerRepository)
    {
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
