<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources\DbPrefill;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Website\DataSources\DataSource;
use Doctrine\Website\Model\Partner;
use Doctrine\Website\Model\PartnerDetails;
use LogicException;

use function array_merge;

class Partners implements DbPrefill
{
    public function __construct(private DataSource $dataSource, private EntityManagerInterface $entityManager)
    {
    }

    public function populate(): void
    {
        foreach ($this->dataSource->getSourceRows() as $sourceRow) {
            $this->buildAndSaveProject($sourceRow);
        }
    }

    /** @param mixed[] $partnerData */
    private function buildAndSaveProject(array $partnerData): void
    {
        if (! isset($partnerData['slug'])) {
            throw new LogicException('Partner slug is required.');
        }

        $name     = (string) ($partnerData['name'] ?? '');
        $slug     = (string) ($partnerData['slug']);
        $url      = (string) ($partnerData['url'] ?? '');
        $logo     = (string) ($partnerData['logo'] ?? '');
        $bio      = (string) ($partnerData['bio'] ?? '');
        $featured = (bool) ($partnerData['featured'] ?? false);

        $details = new PartnerDetails(
            (string) ($partnerData['details']['label'] ?? ''),
            $partnerData['details']['items'] ?? [],
        );
        $this->entityManager->persist($details);

        $utmParameters = array_merge(
            [
                'utm_source'  => 'doctrine',
                'utm_medium'   => 'website',
                'utm_campaign' => 'partners',
            ],
            $partnerData['utmParameters'] ?? [],
        );

        $partner = new Partner($name, $slug, $url, $utmParameters, $logo, $bio, $details, $featured);

        $this->entityManager->persist($partner);
        $this->entityManager->flush();
    }
}
