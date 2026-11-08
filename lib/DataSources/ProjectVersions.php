<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Docs\RST\RSTLanguagesDetector;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Projects\ProjectVersionDeriver;
use Doctrine\Website\Projects\ProjectVersionsReader;
use RuntimeException;

use function array_map;
use function count;
use function end;
use function sprintf;

/**
 * @phpstan-import-type GitProjectVersion from ProjectVersionDeriver
 * @phpstan-import-type ProjectVersionOverride from ProjectVersionDeriver
 * @phpstan-type ProjectDataWithVersions array{
 *     versions?: list<ProjectVersionOverride>,
 *     ...
 * }
 * @phpstan-type ProjectDataForPrepare array{
 *     repositoryName: string,
 *     docsPath: string|null,
 *     docsRepositoryName?: string,
 *     ...
 * }
 */
readonly class ProjectVersions
{
    public function __construct(
        private ProjectGitSyncer $projectGitSyncer,
        private ProjectVersionsReader $projectVersionsReader,
        private RSTLanguagesDetector $rstLanguagesDetector,
        private ProjectVersionDeriver $projectVersionDeriver,
        private string $projectsDir,
    ) {
    }

    /**
     * @param ProjectDataWithVersions&ProjectDataForPrepare $projectData
     *
     * @return array<int, array<string, mixed>>
     */
    public function buildProjectVersions(string $repositoryName, array $projectData): array
    {
        $projectVersions = $this->readProjectVersionsFromGit($repositoryName);
        $projectVersions = $this->projectVersionDeriver->derive($projectVersions, $projectData['versions'] ?? []);

        $this->prepareProjectVersions(
            $repositoryName,
            $projectVersions,
            $projectData,
        );

        return $projectVersions;
    }

    /** @return list<GitProjectVersion> */
    private function readProjectVersionsFromGit(string $repositoryName): array
    {
        $repositoryPath = $this->projectsDir . '/' . $repositoryName;

        return $this->projectVersionsReader->readProjectVersions($repositoryPath);
    }

    /**
     * @param list<array<string, mixed>> $projectVersions
     * @param ProjectDataForPrepare      $projectData
     *
     * @return array<int, array<string, mixed>>
     *
     * @param-out list<array<string, mixed>> $projectVersions
     */
    private function prepareProjectVersions(
        string $repositoryName,
        array &$projectVersions,
        array $projectData,
    ): array {
        $docsRepositoryName = $projectData['docsRepositoryName'] ?? $projectData['repositoryName'];

        $docsDir = $this->projectsDir . '/' . $docsRepositoryName . $projectData['docsPath'];

        foreach ($projectVersions as $key => $projectVersion) {
            if (! isset($projectVersion['branchName'])) {
                if (! isset($projectVersion['tags'])) {
                    throw new RuntimeException(sprintf(
                        <<<'EXCEPTION'
                        Project version "%s" of project "%s" has no branch name and does not have any tags, cannot checkout!
                        EXCEPTION,
                        $projectVersion['name'],
                        $repositoryName,
                    ));
                }

                $this->projectGitSyncer->checkoutTag(
                    $docsRepositoryName,
                    end($projectVersion['tags'])->getName(),
                );
            } else {
                $this->projectGitSyncer->checkoutBranch(
                    $docsRepositoryName,
                    $projectVersion['branchName'],
                );
            }

            $docsLanguages = array_map(static function (RSTLanguage $language): array {
                return [
                    'code' => $language->getCode(),
                    'path' => $language->getPath(),
                ];
            }, $this->rstLanguagesDetector->detectLanguages($docsDir));

            $projectVersions[$key]['hasDocs']       = count($docsLanguages) > 0;
            $projectVersions[$key]['docsLanguages'] = $docsLanguages;

            if (! isset($projectVersion['tags'])) {
                continue;
            }

            $projectVersions[$key]['tags'] = array_map(static function (Tag $tag): array {
                return [
                    'name' => $tag->getName(),
                    'date' => $tag->getDate()->format('Y-m-d H:i:s'),
                ];
            }, $projectVersion['tags']);
        }

        // switch back to master
        $this->projectGitSyncer->checkoutDefaultBranch($repositoryName);

        return $projectVersions;
    }
}
