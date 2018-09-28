<?php

declare(strict_types=1);

namespace Doctrine\Website\Repositories;

use Doctrine\SkeletonMapper\ObjectRepository\BasicObjectRepository;
use Doctrine\Website\Model\DoctrineUser;

class DoctrineUserRepository extends BasicObjectRepository
{
    /**
     * @return DoctrineUser[]
     */
    public function findAll() : array
    {
        /** @var DoctrineUser[] $doctrineUsers */
        $doctrineUsers = parent::findAll();

        return $doctrineUsers;
    }
}
