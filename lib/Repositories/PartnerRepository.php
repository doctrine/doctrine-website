<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\Partner;
use InvalidArgumentException;

use function sprintf;

/**
 * @template T of Partner
 * @template-extends BasicObjectRepository<T>
 */
class PartnerRepository extends BasicObjectRepository
{
    /** @return Partner[] */
    public function findAll(): array
    {
        /** @var Partner[] $partners */
        $partners = parent::findAll();

        return $partners;
    }

    public function findOneBySlug(string $slug): Partner
    {
        $partner = $this->findOneBy(['slug' => $slug]);

        if ($partner === null) {
            throw new InvalidArgumentException(sprintf('Could not find Partner with slug "%s"', $slug));
        }

        return $partner;
    }

    public function findFeaturedPartner(): Partner|null
    {
        return $this->findOneBy(['featured' => true]);
    }
}
