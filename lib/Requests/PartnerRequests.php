<?php

declare(strict_types=1);

namespace Doctrine\Website\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\StaticWebsiteGenerator\Request\RequestCollection;
use Doctrine\Website\Model\Partner;
use Doctrine\Website\Repositories\PartnerRepository;

class PartnerRequests
{
    /** @var PartnerRepository */
    private $partnerRepository;

    public function __construct(PartnerRepository $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
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
