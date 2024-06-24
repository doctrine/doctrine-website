<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\Website\Model\DoctrineUser;

/**
 * @template T of DoctrineUser
 * @template-extends EntityRepository<T>
 */
class DoctrineUserRepository extends EntityRepository
{
}
