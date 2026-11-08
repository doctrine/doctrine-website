<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Website\Git\Tag;

use function preg_match;
use function usort;
use function version_compare;

/**
 * Derives the state of every project version from the git tags and branches.
 *
 * Flags are computed from git. Values present in .doctrine-project.json
 * override the computed flags.
 *
 * @phpstan-type GitProjectVersion array{
 *     name: string,
 *     slug?: string,
 *     branchName: string|null,
 *     tags: list<Tag>
 * }
 * @phpstan-type MergedProjectVersion array{
 *     name: string,
 *     branchName: string|null,
 *     tags: list<Tag>|null,
 *     override: ProjectVersionOverride
 * }
 * @phpstan-type ProjectVersionOverride array{
 *     name: string,
 *     branchName?: string|null,
 *     slug?: string,
 *     current?: bool,
 *     upcoming?: bool,
 *     maintained?: bool,
 *     lifeSupport?: bool,
 *     aliases?: list<string>
 * }
 */
final class ProjectVersionDeriver
{
    /**
     * @param list<GitProjectVersion>      $gitVersions
     * @param list<ProjectVersionOverride> $overrides
     *
     * @return list<array<string, mixed>>
     */
    public function derive(array $gitVersions, array $overrides): array
    {
        $overridesByName = [];
        foreach ($overrides as $override) {
            $overridesByName[$override['name']] = $override;
        }

        $versions = $this->merge($gitVersions, $overridesByName);
        $versions = $this->sort($versions);

        $current        = $this->findCurrent($versions, $overridesByName);
        $latestAssigned = $this->hasExplicitLatestSlug($overridesByName);

        $derived = [];

        foreach ($versions as $version) {
            $override = $version['override'];

            $isStandard = $this->isStandardVersion($version['name']);
            $isCurrent  = $override['current'] ?? $version['name'] === $current;
            $isUpcoming = $override['upcoming'] ?? (
                $isStandard
                && $current !== null
                && version_compare($version['name'], $current, '>')
                && ! $this->hasStableRelease($version['tags'] ?? [])
            );

            $slug = $override['slug'] ?? null;
            if ($slug === null) {
                $slug           = $latestAssigned ? $version['name'] : 'latest';
                $latestAssigned = true;
            }

            $lifeSupport = $override['lifeSupport'] ?? false;

            $versionData = [
                'name' => $version['name'],
                'slug' => $slug,
                'branchName' => $version['branchName'],
                'current' => $isCurrent && ! $lifeSupport,
                'upcoming' => $isUpcoming && ! $lifeSupport,
                'maintained' => $override['maintained'] ?? ($isCurrent || $isUpcoming),
                'lifeSupport' => $lifeSupport,
                'aliases' => $override['aliases'] ?? [],
            ];

            if ($lifeSupport) {
                $versionData['maintained'] = true;
                $versionData['current']    = false;
                $versionData['upcoming']   = false;
            }

            if (isset($version['tags'])) {
                $versionData['tags'] = $version['tags'];
            }

            $derived[] = $versionData;
        }

        return $derived;
    }

    /**
     * @param list<GitProjectVersion>               $gitVersions
     * @param array<string, ProjectVersionOverride> $overrides
     *
     * @return list<MergedProjectVersion>
     */
    private function merge(array $gitVersions, array $overrides): array
    {
        $gitVersionsByName = [];
        foreach ($gitVersions as $gitVersion) {
            $gitVersionsByName[$gitVersion['name']] = $gitVersion;
        }

        $versions = [];

        foreach ($gitVersions as $gitVersion) {
            $override = $overrides[$gitVersion['name']] ?? ['name' => $gitVersion['name']];

            $versions[] = [
                'name' => $gitVersion['name'],
                // The branch guessed from git takes precedence over the configured branch.
                'branchName' => $gitVersion['branchName'] ?? $override['branchName'] ?? null,
                'tags' => $gitVersion['tags'],
                'override' => $override,
            ];
        }

        foreach ($overrides as $name => $override) {
            if (isset($gitVersionsByName[$name])) {
                continue;
            }

            $versions[] = [
                'name' => $name,
                'branchName' => $override['branchName'] ?? null,
                'tags' => null,
                'override' => $override,
            ];
        }

        return $versions;
    }

    /**
     * @param list<MergedProjectVersion> $versions
     *
     * @return list<MergedProjectVersion>
     */
    private function sort(array $versions): array
    {
        usort(
            $versions,
            static fn (array $a, array $b): int => version_compare($b['name'], $a['name']),
        );

        return $versions;
    }

    /**
     * @param list<MergedProjectVersion>            $versions
     * @param array<string, ProjectVersionOverride> $overridesByName
     */
    private function findCurrent(array $versions, array $overridesByName): string|null
    {
        foreach ($overridesByName as $name => $override) {
            if (($override['current'] ?? false) === true) {
                return $name;
            }
        }

        foreach ($versions as $version) {
            if (! $this->isStandardVersion($version['name'])) {
                continue;
            }

            if ($this->hasStableRelease($version['tags'] ?? [])) {
                return $version['name'];
            }
        }

        return null;
    }

    /** @param array<string, ProjectVersionOverride> $overrides */
    private function hasExplicitLatestSlug(array $overrides): bool
    {
        foreach ($overrides as $override) {
            if (($override['slug'] ?? null) === 'latest') {
                return true;
            }
        }

        return false;
    }

    private function isStandardVersion(string $name): bool
    {
        return preg_match('/^\d+\.\d+$/', $name) === 1;
    }

    /** @param list<Tag> $tags */
    private function hasStableRelease(array $tags): bool
    {
        foreach ($tags as $tag) {
            if ($tag->isStableRelease()) {
                return true;
            }
        }

        return false;
    }
}
