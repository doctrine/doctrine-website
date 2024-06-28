<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\Website\Model\SitemapPage;

/**
 * @template T of SitemapPage
 * @template-extends EntityRepository<T>
 */
class SitemapPageRepository extends EntityRepository
{
    /** @inheritDoc */
    public function findAll(): array
    {
        return $this->findBy([], ['url' => 'asc']);
    }
}
