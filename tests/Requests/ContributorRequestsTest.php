<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Requests;

use Doctrine\SkeletonMapper\ObjectManagerInterface;
use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\Website\Model\Contributor;
use Doctrine\Website\Repositories\ContributorRepository;
use Doctrine\Website\Requests\ContributorRequests;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContributorRequestsTest extends TestCase
{
    /** @var ContributorRepository|MockObject */
    private $contributorRepository;

    /** @var ContributorRequests */
    private $contributorRequests;

    public function testGetContributors() : void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);

        $contributor1 = new Contributor();
        $contributor1->hydrate(['github' => 'github1'], $objectManager);

        $contributor2 = new Contributor();
        $contributor2->hydrate(['github' => 'github2'], $objectManager);

        $contributors = [$contributor1, $contributor2];

        $this->contributorRepository->expects(self::once())
            ->method('findAll')
            ->willReturn($contributors);

        $contributorRequests = $this->contributorRequests->getContributors();

        self::assertEquals(new ArrayRequestCollection([
            ['github' => 'github1'],
            ['github' => 'github2'],
        ]), $contributorRequests);
    }

    protected function setUp() : void
    {
        $this->contributorRepository = $this->createMock(ContributorRepository::class);

        $this->contributorRequests = new ContributorRequests(
            $this->contributorRepository
        );
    }
}
