<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Docs\RST\RSTLanguagesDetector;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Projects\ProjectGitSyncer;
use Doctrine\Website\Projects\ProjectVersionsReader;
use RuntimeException;

use function array_filter;
use function array_map;
use function array_values;
use function count;
use function end;
use function in_array;
use function sprintf;
use function strnatcmp;
use function usort;

readonly class ProjectVersions
{
    public function __construct(
        private ProjectGitSyncer $projectGitSyncer,
        private ProjectVersionsReader $projectVersionsReader,
        private RSTLanguagesDetector $rstLanguagesDetector,
        private string $projectsDir,
    ) {
    }

    /**
     * @param array{versions: list<array{name:string, branchName?: string|null}>} $projectData
     *
     * @return mixed[]
     */
    public function buildProjectVersions(string $repositoryName, array $projectData): array
    {
        $projectVersions = $this->readProjectVersionsFromGit($repositoryName);
        $projectVersions = $this->removeUnwantedVersions($projectVersions, $projectData);
        $projectVersions = $this->applyConfiguredProjectVersions($projectVersions, $projectData);

        $projectVersions = $this->sortProjectVersions($projectVersions);

        $this->prepareProjectVersions(
            $repositoryName,
            $projectVersions,
            $projectData,
        );

        return $projectVersions;
    }

    /**
     * @param list<array{name: string, slug: string, branchName: string|null, tags: non-empty-list<Tag>}> $projectVersions
     * @param array{versions: list<array{name:string, branchName?: string|null}>}                         $projectData
     *
     * @return list<array{name: string, slug: string, branchName: string|null, tags: non-empty-list<Tag>}>
     */
    private function removeUnwantedVersions(array $projectVersions, array $projectData): array
    {
        $allowedVersions = array_map(static fn (array $versions) => $versions['name'], $projectData['versions']);
        $projectVersions = array_filter($projectVersions, static fn (array $version): bool => in_array($version['name'], $allowedVersions, true));

        return array_values($projectVersions);
    }

    /** @return list<array{name: string, slug: string, branchName: string|null, tags: non-empty-list<Tag>}> */
    private function readProjectVersionsFromGit(string $repositoryName): array
    {
        $repositoryPath = $this->projectsDir . '/' . $repositoryName;

        $projectVersions = $this->projectVersionsReader->readProjectVersions($repositoryPath);

        // fix this, we shouldn't have null branch names at this point. Fix it further upstream

        /** @phpstan-ignore return.type */
        return array_filter($projectVersions, static function (array $projectVersion): bool {
            return count($projectVersion['tags']) > 0;
        });
    }

    /**
     * @param list<array{name: string, slug: string, branchName: string|null, tags: list<Tag>}> $projectVersions
     * @param array{versions: list<array{name:string, branchName?: string|null}>}               $projectData
     *
     * @return list<array{
     *             name: string,
     *             branchName?: string|null
     *         }|array{
     *             name: string,
     *             slug: string,
     *             branchName: string|null,
     *             tags: non-empty-list<Tag>,
     *             maintained?: false
     *         }>
     */
    private function applyConfiguredProjectVersions(
        array $projectVersions,
        array $projectData,
    ): array {
        foreach ($projectVersions as $key => $projectVersion) {
            $configured = false;

            foreach ($projectData['versions'] as $k => $version) {
                if ($this->containsSameProjectVersion($projectVersion, $version)) {
                    $version['tags'] = $projectVersion['tags'];

                    $version['branchName'] = $projectVersion['branchName'];

                    $projectVersions[$key] = $version;

                    unset($projectData['versions'][$k]);

                    $configured = true;

                    break;
                }
            }

            if ($configured !== false) {
                continue;
            }

            $projectVersions[$key]['maintained'] = false;
        }

        foreach ($projectData['versions'] as $projectVersion) {
            $projectVersions[] = $projectVersion;
        }

        return $projectVersions;
    }

    /**
     * @param array{name: string, slug: string, branchName: string|null, tags: list<Tag>} $a
     * @param array{name: string, branchName?: string|null}                               $b
     */
    private function containsSameProjectVersion(array $a, array $b): bool
    {
        if ($a['name'] === $b['name']) {
            return true;
        }

        if (! isset($b['branchName'])) {
            return false;
        }

        return $a['branchName'] === $b['branchName'];
    }

    /**
     * @param list<array{name: string, branchName?: string|null}|array{name: string, slug: string, branchName: string|null, tags: non-empty-list<Tag>, maintained?: false}> $projectVersions
     * @param mixed[]                                                                                                                                                       $projectData
     *
     * @return mixed[]
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

    /**
     * @param T $projectVersions
     *
     * @return T
     *
     * @template T of mixed[]
     */
    private function sortProjectVersions(array $projectVersions): array
    {
        // sort by name so newest versions are first
        usort($projectVersions, static function (array $a, array $b): int {
            return strnatcmp($b['name'], $a['name']);
        });

        /** @phpstan-ignore return.type */
        return $projectVersions;
    }
}
