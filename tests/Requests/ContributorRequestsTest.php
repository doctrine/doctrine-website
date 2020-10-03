<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Requests;

use Doctrine\StaticWebsiteGenerator\Request\ArrayRequestCollection;
use Doctrine\Website\Repositories\ContributorRepository;
use Doctrine\Website\Requests\ContributorRequests;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ContributorRequestsTest extends TestCase
{
    /** @var ContributorRepository|MockObject */
    private $contributorRepository;

    /** @var ContributorRequests */
    private $contributorRequests;

    public function testGetContributors(): void
    {
        $contributor1 = $this->createContributor(['github' => 'github1']);

        $contributor2 = $this->createContributor(['github' => 'github2']);

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

    protected function setUp(): void
    {
        $this->contributorRepository = $this->createMock(ContributorRepository::class);

        $this->contributorRequests = new ContributorRequests(
            $this->contributorRepository
        );
    }
}
