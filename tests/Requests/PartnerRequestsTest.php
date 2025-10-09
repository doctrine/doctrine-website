<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Requests;

use Doctrine\Website\Model\Partner;
use Doctrine\Website\Model\PartnerDetails;
use Doctrine\Website\Repositories\PartnerRepository;
use Doctrine\Website\Requests\PartnerRequests;
use Doctrine\Website\StaticGenerator\Request\ArrayRequestCollection;
use Doctrine\Website\Tests\TestCase;

class PartnerRequestsTest extends TestCase
{
    public function testGetPartners(): void
    {
        $partner           = new Partner('test', 'partner', '', [], '', '', new PartnerDetails('', []), false);
        $partnerRepository = $this->createMock(PartnerRepository::class);
        $partnerRepository->expects(self::once())
            ->method('findAll')
            ->willReturn([$partner]);

        $partnerRequest = new PartnerRequests($partnerRepository);
        $partners       = $partnerRequest->getPartners();

        $expects = new ArrayRequestCollection([
            ['slug' => 'partner'],
        ]);

        self::assertEquals($expects, $partners);
    }
}
