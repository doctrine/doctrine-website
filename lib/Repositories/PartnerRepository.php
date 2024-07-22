<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\Website\Model\Partner;
use InvalidArgumentException;

use function sprintf;

/**
 * @template T of Partner
 * @template-extends EntityRepository<T>
 */
class PartnerRepository extends EntityRepository
{
    public function findOneBySlug(string $slug): Partner
    {
        $partner = $this->find($slug);

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
