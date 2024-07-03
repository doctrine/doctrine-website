<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\Website\Model\BlogPost;
use InvalidArgumentException;

/**
 * @template T of BlogPost
 * @template-extends EntityRepository<T>
 */
class BlogPostRepository extends EntityRepository
{
    /** @inheritDoc */
    public function findAll(): array
    {
        return $this->findBy([], ['date' => 'desc']);
    }

    /** @return BlogPost[] */
    public function findPaginated(int $page = 1, int $perPage = 10): array
    {
        if ($page < 1 || $perPage < 1) {
            throw new InvalidArgumentException('Pagination parameters must be positive.');
        }

        $offset = ($page - 1) * $perPage;

        return $this->findBy([], ['date' => 'desc'], $perPage, $offset);
    }
}
