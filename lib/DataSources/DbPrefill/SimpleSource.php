<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources\DbPrefill;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Website\DataSources\DataSource;

class SimpleSource implements DbPrefill
{
    /** @phpstan-param class-string $modelClassName */
    public function __construct(private string $modelClassName, private DataSource $dataSource, private EntityManagerInterface $entityManager)
    {
    }

    public function populate(): void
    {
        foreach ($this->dataSource->getSourceRows() as $sourceRow) {
            $entity = $this->createObject($sourceRow);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    /** @param mixed[] $data */
    private function createObject(array $data): object
    {
        return new $this->modelClassName(...$data);
    }
}
