<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use Doctrine\Website\Projects\ProjectDataRepository;
use PHPUnit\Framework\TestCase;

class ProjectDataRepositoryTest extends TestCase
{
    public function testGetProjectRepositoryNames(): void
    {
        $projectDataRepository = new ProjectDataRepository([
            ['repositoryName' => 'orm'],
            ['repositoryName' => 'dbal'],
        ]);

        self::assertSame(['orm', 'dbal'], $projectDataRepository->getProjectRepositoryNames());
    }
}
