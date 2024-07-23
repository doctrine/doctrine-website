<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources\DbPrefill;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Website\DataSources\DataSource;
use Doctrine\Website\DataSources\DbPrefill\Partners;
use Doctrine\Website\Model\Partner;
use Doctrine\Website\Tests\TestCase;

use function assert;
use function file_get_contents;
use function is_dir;
use function json_decode;

class PartnersTest extends TestCase
{
    protected function setUp(): void
    {
        $buildDir = __DIR__ . '/../../../build-test';

        if (is_dir($buildDir)) {
            return;
        }

        self::markTestSkipped('This test requires ./bin/console build-website to have been run.');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $entityManager = $this->getEntityManager();
        $repository    = $entityManager->getRepository(Partner::class);
        $partner       = $repository->find('testpartner');

        assert($partner instanceof Partner);

        $entityManager->remove($partner);
        $entityManager->flush();
    }

    public function testPopulate(): void
    {
        $partnersFixture = __DIR__ . '/fixtures/partners.json';
        $fixture         = json_decode((string) file_get_contents($partnersFixture), true);

        $entityManager = $this->getEntityManager();

        $dataSource = $this->createMock(DataSource::class);
        $dataSource->method('getSourceRows')->willReturn($fixture);

        $dbFill = new Partners($dataSource, $entityManager);
        $dbFill->populate();

        $this->assertPartnerIsComplete($entityManager);
    }

    private function assertPartnerIsComplete(EntityManagerInterface $entityManager): void
    {
        $entityManager->clear();

        $repository = $entityManager->getRepository(Partner::class);
        $partner    = $repository->find('testpartner');

        assert($partner instanceof Partner);

        self::assertSame('Name', $partner->getName());
        self::assertSame('testpartner', $partner->getSlug());
        self::assertSame('testpartner.svg', $partner->getLogo());
        self::assertSame('https://testpartner.bird', $partner->getUrl());
        self::assertSame('bio', $partner->getBio());
        self::assertTrue($partner->isFeatured());
        self::assertSame('https://testpartner.bird?utm_source=source&utm_medium=medium&utm_campaign=campaign', $partner->getUrlWithUtmParameters());

        $details = $partner->getDetails();

        self::assertSame('Features', $details->getLabel());
        self::assertSame(['Stuff', 'More Stuff'], $details->getItems());
    }

    private function getEntityManager(): EntityManagerInterface
    {
        $entityManager = $this->getContainer()->get(EntityManagerInterface::class);
        assert($entityManager instanceof EntityManagerInterface);

        return $entityManager;
    }
}
