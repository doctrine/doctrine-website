<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Closure;
use Doctrine\Website\Projects\Project;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Twig_Extension;
use Twig_SimpleFunction;
use function array_filter;
use function file_get_contents;
use function filemtime;
use function in_array;
use function ksort;
use function md5;
use function realpath;
use function str_replace;
use function strpos;
use function substr;

class MainExtension extends Twig_Extension
{
    /** @var array */
    private $teamMembers;

    public function __construct(array $teamMembers)
    {
        $this->teamMembers = $teamMembers;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('get_asset_url', [$this, 'getAssetUrl']),
            new Twig_SimpleFunction('get_active_core_team_members', [$this, 'getActiveCoreTeamMembers']),
            new Twig_SimpleFunction('get_active_documentation_team_members', [$this, 'getActiveDocumentationTeamMembers']),
            new Twig_SimpleFunction('get_inactive_team_members', [$this, 'getInactiveTeamMembers']),
            new Twig_SimpleFunction('get_active_project_team_members', [$this, 'getActiveProjectTeamMembers']),
            new Twig_SimpleFunction('get_inactive_project_team_members', [$this, 'getInactiveProjectTeamMembers']),
            new Twig_SimpleFunction('get_all_project_team_members', [$this, 'getAllProjectTeamMembers']),
            new Twig_SimpleFunction('get_docs_urls', [$this, 'getDocsUrls']),
            new Twig_SimpleFunction('get_api_docs_urls', [$this, 'getApiDocsUrls']),
        ];
    }

    public function getAssetUrl(string $path, string $siteUrl)
    {
        return $siteUrl . $path . '?' . $this->getAssetCacheBuster($path);
    }

    public function getTeamMembers(?Closure $filter = null) : array
    {
        $teamMembers = [];

        foreach ($this->teamMembers as $key => $teamMember) {
            $name = $teamMember['name'] ?? $key;

            $teamMembers[$name] = $teamMember;
        }

        if ($filter !== null) {
            $teamMembers = array_filter($teamMembers, $filter);
        }

        ksort($teamMembers);

        return $teamMembers;
    }

    public function getActiveCoreTeamMembers() : array
    {
        return $this->getTeamMembers(function (array $teamMember) {
            $active = $teamMember['active'] ?? false;
            $core   = $teamMember['core'] ?? false;

            return $active && $core;
        });
    }

    public function getActiveDocumentationTeamMembers() : array
    {
        return $this->getTeamMembers(function (array $teamMember) {
            $active        = $teamMember['active'] ?? false;
            $documentation = $teamMember['documentation'] ?? false;

            return $active && $documentation;
        });
    }

    public function getInactiveTeamMembers() : array
    {
        return $this->getTeamMembers(function (array $teamMember) {
            $active = $teamMember['active'] ?? false;

            return $active === false;
        });
    }

    public function getAllProjectTeamMembers(Project $project) : array
    {
        return $this->getTeamMembers(function (array $teamMember) use ($project) {
            return in_array($project->getSlug(), $teamMember['projects'] ?? []);
        });
    }

    public function getActiveProjectTeamMembers(Project $project) : array
    {
        return $this->getTeamMembers(function (array $teamMember) use ($project) {
            $active = $teamMember['active'] ?? false;

            return $active && in_array($project->getSlug(), $teamMember['projects'] ?? []);
        });
    }

    public function getInactiveProjectTeamMembers(Project $project) : array
    {
        return $this->getTeamMembers(function (array $teamMember) use ($project) {
            $active = $teamMember['active'] ?? false;

            return ! $active && in_array($project->getSlug(), $teamMember['projects'] ?? []);
        });
    }

    public function getDocsUrls() : array
    {
        return $this->getUrlsFromFiles('projects');
    }

    public function getApiDocsUrls() : array
    {
        return $this->getUrlsFromFiles('api');
    }

    private function getUrlsFromFiles(string $path)
    {
        $root = realpath(__DIR__ . '/../../../../../source');
        $path = $root . '/' . $path;

        $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);

        $urls = [];
        foreach (new RecursiveIteratorIterator($it) as $file) {
            $path = (string) $file;

            if (strpos($path, '.html') === false) {
                continue;
            }

            $url = str_replace($root, '', $path);

            $urls[] = [
                'url' => $url,
                'date' => filemtime($path),
            ];
        }

        return $urls;
    }

    private function getAssetCacheBuster(string $path) : string
    {
        $assetPath = realpath(__DIR__ . '/../../../../../source/' . $path);

        return substr(md5(file_get_contents($assetPath)), 0, 6);
    }
}
