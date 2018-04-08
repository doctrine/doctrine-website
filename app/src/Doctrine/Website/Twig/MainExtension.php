<?php

namespace Doctrine\Website\Twig;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleTest;

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
            new Twig_SimpleFunction('get_active_team_members', [$this, 'getActiveTeamMembers']),
            new Twig_SimpleFunction('get_inactive_team_members', [$this, 'getInactiveTeamMembers']),
            new Twig_SimpleFunction('get_active_project_team_members', [$this, 'getActiveProjectTeamMembers']),
            new Twig_SimpleFunction('get_inactive_project_team_members', [$this, 'getInactiveProjectTeamMembers']),
            new Twig_SimpleFunction('get_all_project_team_members', [$this, 'getAllProjectTeamMembers']),
            new Twig_SimpleFunction('get_docs_urls', [$this, 'getDocsUrls']),
            new Twig_SimpleFunction('get_api_docs_urls', [$this, 'getApiDocsUrls'])
        ];
    }

    public function getAssetUrl(string $path, string $siteUrl)
    {
        return $siteUrl.$path.'?'.$this->getAssetCacheBuster($path);
    }

    public function getAllTeamMembers() : array
    {
        $teamMembers = [];

        foreach ($this->teamMembers as $key => $teamMember) {
            $key = $teamMember['name'] ?? $key;

            $teamMembers[$key] = $teamMember;
        }

        ksort($teamMembers);

        return $teamMembers;
    }

    public function getActiveTeamMembers() : array
    {
        $teamMembers = [];

        foreach ($this->teamMembers as $key => $teamMember) {
            $active = $teamMember['active'] ?? false;

            if (!$active) {
                continue;
            }

            $key = $teamMember['name'] ?? $key;

            $teamMembers[$key] = $teamMember;
        }

        ksort($teamMembers);

        return $teamMembers;
    }

    public function getInactiveTeamMembers() : array
    {
        $teamMembers = [];

        foreach ($this->teamMembers as $key => $teamMember) {
            $active = $teamMember['active'] ?? false;

            if ($active) {
                continue;
            }

            $key = $teamMember['name'] ?? $key;

            $teamMembers[$key] = $teamMember;
        }

        ksort($teamMembers);

        return $teamMembers;
    }

    public function getAllProjectTeamMembers(Project $project) : array
    {
        return array_filter($this->getAllTeamMembers(), function(array $teamMember) use ($project) {
            return in_array($project->getSlug(), $teamMember['projects'] ?? []);
        });
    }

    public function getActiveProjectTeamMembers(Project $project) : array
    {
        return array_filter($this->getAllTeamMembers(), function(array $teamMember) use ($project) {
            $active = $teamMember['active'] ?? false;

            return $active && in_array($project->getSlug(), $teamMember['projects'] ?? []);
        });
    }

    public function getInactiveProjectTeamMembers(Project $project) : array
    {
        return array_filter($this->getAllTeamMembers(), function(array $teamMember) use ($project) {
            $active = $teamMember['active'] ?? false;

            return !$active && in_array($project->getSlug(), $teamMember['projects'] ?? []);
        });
    }

    public function getDocsUrls() : array
    {
        $root = realpath(__DIR__.'/../../../../../source');
        $path = $root.'/projects';

        $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);

        $urls = [];
        foreach(new RecursiveIteratorIterator($it) as $file) {
            $url = str_replace($root, '', $file);

            $urls[] = [
                'url' => $url,
                'date' => filemtime($file),
            ];
        }

        return $urls;
    }

    public function getApiDocsUrls() : array
    {
        $root = realpath(__DIR__.'/../../../../../source');
        $path = $root.'/api';

        $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);

        $urls = [];
        foreach(new RecursiveIteratorIterator($it) as $file) {
            $url = str_replace($root, '', $file);

            $urls[] = [
                'url' => $url,
                'date' => filemtime($file),
            ];
        }

        return $urls;
    }

    private function getAssetCacheBuster(string $path) : string
    {
        $assetPath = realpath(__DIR__.'/../../../../../source/'.$path);

        return substr(md5(file_get_contents($assetPath)), 0, 6);
    }
}
