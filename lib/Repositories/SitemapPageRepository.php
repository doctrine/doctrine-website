<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\SitemapPage;

class SitemapPageRepository extends BasicObjectRepository
{
    /**
     * @return SitemapPage[]
     */
    public function findAll() : array
    {
        /** @var SitemapPage[] $sitemapPages */
        $sitemapPages = $this->findBy([], ['url' => 'asc']);

        return $sitemapPages;
    }
}
