<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use DateTimeImmutable;
use Doctrine\Website\DataSources\Projects;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Docs\RST\RSTLanguagesDetector;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Projects\GetProjectPackagistData;
use Doctrine\Website\Projects\ProjectDataReader;
use Doctrine\Website\Projects\ProjectDataRepository;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Projects\ProjectVersionsReader;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProjectsTest extends TestCase
{
    private ProjectDataRepository&MockObject $projectDataRepository;

    private ProjectGitSyncer&MockObject $projectGitSyncer;

    private ProjectDataReader&MockObject $projectDataReader;

    private ProjectVersionsReader&MockObject $projectVersionsReader;

    private RSTLanguagesDetector&MockObject $rstLanguagesDetector;

    private GetProjectPackagistData&MockObject $getProjectPackagistData;

    private string $projectsDir;

    private Projects $dataSource;

    public function testBuild(): void
    {
        $this->projectDataRepository->expects(self::once())
            ->method('getProjectRepositoryNames')
            ->willReturn(['orm']);

        $this->projectGitSyncer->expects(self::exactly(2))
            ->method('checkoutDefaultBranch')
            ->with('orm');

        $this->projectDataReader->expects(self::once())
            ->method('read')
            ->with('orm')
            ->willReturn([
                'composerPackageName' => 'doctrine/orm',
                'repositoryName' => 'orm',
                'docsPath' => '/docs',
                'versions' => [
                    [
                        'name' => '1.0',
                        'branchName' => null,
                    ],
                    [
                        'name' => '1.1',
                        'branchName' => '1.1',
                    ],
                    ['name' => '1.2'],
                ],
            ]);

        $this->projectVersionsReader->expects(self::once())
            ->method('readProjectVersions')
            ->with('/path/to/projects/orm')
            ->willReturn([
                [
                    'name' => '1.0',
                    'branchName' => null,
                    'tags' => [
                        new Tag('1.0.0', new DateTimeImmutable('2019-09-01')),
                        new Tag('1.0.1', new DateTimeImmutable('2019-09-02')),
                    ],
                ],
                [
                    'name' => '1.1',
                    'branchName' => '1.1',
                    'tags' => [
                        new Tag('1.1.0', new DateTimeImmutable('2019-09-03')),
                        new Tag('1.1.1', new DateTimeImmutable('2019-09-04')),
                    ],
                ],
                [
                    'name' => '1.2',
                    'branchName' => null,
                    'tags' => [
                        new Tag('1.2.0', new DateTimeImmutable('2019-09-05')),
                    ],
                ],
            ]);

        $this->projectGitSyncer->expects(self::once())
            ->method('checkoutBranch')
            ->with('orm', '1.1');

        $this->rstLanguagesDetector->expects(self::exactly(3))
            ->method('detectLanguages')
            ->with('/path/to/projects/orm/docs')
            ->willReturnOnConsecutiveCalls(
                [
                    new RSTLanguage('en', '/path/to/en'),
                ],
                [],
                [],
            );

        $this->projectGitSyncer->expects(self::exactly(2))
            ->method('checkoutTag')
            ->willReturnMap([
                ['orm', '1.0.1', null],
                ['orm', '1.2.0', null],
            ]);

        $this->getProjectPackagistData->expects(self::once())
            ->method('__invoke')
            ->with('doctrine/orm')
            ->willReturn(['package' => []]);

        $data = $this->dataSource->getSourceRows();

        $expected = [
            [
                'active' => true,
                'archived' => false,
                'integration' => false,
                'composerPackageName' => 'doctrine/orm',
                'repositoryName' => 'orm',
                'docsPath' => '/docs',
                'versions' => [
                    [
                        'name' => '1.2',
                        'tags' => [
                            [
                                'name' => '1.2.0',
                                'date' => '2019-09-05 00:00:00',
                            ],
                        ],
                        'branchName' => null,
                        'hasDocs' => true,
                        'docsLanguages' => [
                            [
                                'code' =>  'en',
                                'path' => '/path/to/en',
                            ],
                        ],
                    ],
                    [
                        'name' => '1.1',
                        'branchName' => '1.1',
                        'tags' => [
                            [
                                'name' => '1.1.0',
                                'date' => '2019-09-03 00:00:00',
                            ],
                            [
                                'name' => '1.1.1',
                                'date' => '2019-09-04 00:00:00',
                            ],
                        ],
                        'hasDocs' => false,
                        'docsLanguages' => [],
                    ],
                    [
                        'name' => '1.0',
                        'branchName' => null,
                        'tags' => [
                            [
                                'name' => '1.0.0',
                                'date' => '2019-09-01 00:00:00',
                            ],
                            [
                                'name' => '1.0.1',
                                'date' => '2019-09-02 00:00:00',
                            ],
                        ],
                        'hasDocs' => false,
                        'docsLanguages' => [],
                    ],
                ],
                'packagistData' => ['package' => []],
            ],
        ];

        self::assertSame($expected, $data);
    }

    protected function setUp(): void
    {
        $this->projectDataRepository   = $this->createMock(ProjectDataRepository::class);
        $this->projectGitSyncer        = $this->createMock(ProjectGitSyncer::class);
        $this->projectDataReader       = $this->createMock(ProjectDataReader::class);
        $this->projectVersionsReader   = $this->createMock(ProjectVersionsReader::class);
        $this->rstLanguagesDetector    = $this->createMock(RSTLanguagesDetector::class);
        $this->getProjectPackagistData = $this->createMock(GetProjectPackagistData::class);
        $this->projectsDir             = '/path/to/projects';

        $this->dataSource = new Projects(
            $this->projectDataRepository,
            $this->projectGitSyncer,
            $this->projectDataReader,
            $this->projectVersionsReader,
            $this->rstLanguagesDetector,
            $this->getProjectPackagistData,
            $this->projectsDir,
        );
    }
}
