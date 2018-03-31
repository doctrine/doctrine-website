<?php

namespace Doctrine\Website\Docs;

use Doctrine\Website\Docs;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Doctrine\Website\SculpinRstBundle\Kernel\Kernel;
use Symfony\Component\Console\Output\OutputInterface;

class Preparer
{
    private const SCULPIN_TEMPLATE = <<<TEMPLATE
---
layout: default
indexed: true
title: %s
menuSlug: projects
docsSlug: %s
docsPage: true
docsIndex: %s
docsVersion: %s
permalink: none
---
%s
TEMPLATE;

    /** @var string */
    private $sculpinSourcePath;

    /** @var string */
    private $projectsPath;

    /** @var Kernel */
    private $kernel;

    /** @var Project */
    private $project;

    /** @var ProjectVersion */
    private $version;

    /** @var string */
    private $tmpPath;

    public function __construct(
        string $sculpinSourcePath,
        string $projectsPath,
        Kernel $kernel,
        Project $project,
        ProjectVersion $version)
    {
        $this->sculpinSourcePath = $sculpinSourcePath;
        $this->projectsPath = $projectsPath;
        $this->kernel = $kernel;
        $this->project = $project;
        $this->version = $version;
        $this->tmpPath = sys_get_temp_dir().'/doctrine-docs';
    }

    public function versionHasDocs(Project $project, ProjectVersion $version) : bool
    {
        return file_exists($this->getProjectDocsPath().'/en/index.rst');
    }

    public function prepareGit(OutputInterface $output)
    {
        $dir = $this->getProjectDocsRepositoryPath();

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);

            $command = sprintf('git clone git@github.com:doctrine/%s.git %s',
                $this->project->getDocsRepositoryName(),
                $this->getProjectDocsRepositoryPath()
            );

            $this->shellExec($command);
        }

        $command = sprintf('cd %s && git fetch && git clean -f && git reset --hard master && git checkout -- && git pull origin %s',
            $this->getProjectDocsRepositoryPath(), $this->version->getBranchName()
        );

        $this->shellExec($command);
    }

    public function prepare(OutputInterface $output)
    {
        // copy the docs all in to one location first
        $this->copyRst($output);

        // build the docs and produce html for sculpin to process
        $this->buildRst($output);

        // prepare all the generated html for the sculpin build
        $this->postRstBuild($output);
    }

    private function copyRst(OutputInterface $output)
    {
        // TODO delete first before copying again to clean out previous build

        $files = $this->findFiles($this->getProjectDocsPath().'/en');

        foreach ($files as $file) {
            if (strpos($file, '.rst') === false) {
                continue;
            }

            $path = str_replace($this->getProjectDocsPath().'/en/', '', $file);

            $newPath = $this->tmpPath.'/'.$this->project->getDocsSlug().'/en/'.$this->version->getSlug().'/'.$path;

            $this->ensureDirectoryExists(dirname($newPath));

            $content = trim(file_get_contents($file));

            // fix incorrect casing of note
            $content = str_replace('.. Note::', '.. note::', $content);

            // TODO grab contents out of <body>

            $title = explode("\n", $content)[0];

            file_put_contents($newPath, $content);
        }
    }

    private function buildRst(OutputInterface $output)
    {
        $builder = new RstBuilder($this->kernel);

        $builder->build(
            $this->tmpPath.'/'.$this->project->getDocsSlug().'/en/'.$this->version->getSlug(),
            $this->sculpinSourcePath.'/projects/'.$this->project->getDocsSlug().'/en/'.$this->version->getSlug(),
            $output->isVerbose()
        );
    }

    public function postRstBuild(OutputInterface $output)
    {
        $projectDocsVersionPath = $this->sculpinSourcePath.'/projects/'.$this->project->getDocsSlug().'/en/'.$this->version->getSlug();

        $files = $this->findFiles($projectDocsVersionPath);

        foreach ($files as $file) {
            $content = trim(file_get_contents($file));

            // extract title from <h1>
            preg_match('/<h1>(.*)<\/h1>/', $content, $matches);

            $title = '';
            if ($matches) {
                $title = $matches[1];
            }

            // modify anchors and headers
            $content = preg_replace(
                '/<a id="(.*)"><\/a><h(\d)>(.*)<\/h(\d)>/',
                '<h$2 class="section-header" id="$1"><a href="#$1">$3<i class="fas fa-link"></i></a></h$2>',
                $content
            );

            $newContent = sprintf(self::SCULPIN_TEMPLATE,
                $title,
                $this->project->getDocsSlug(),
                strpos($file, 'index.html') !== false ? 'true' : 'false',
                $this->version->getSlug(),
                $content
            );

            file_put_contents($file, $newContent);
        }
    }

    private function ensureDirectoryExists(string $dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    private function findFiles(string $root) : array
    {
        return $this->recursiveGlob($root.'/*');
    }

    private function getProjectDocsRepositoryPath() : string
    {
        return $this->projectsPath.'/'.$this->project->getDocsRepositoryName();
    }

    private function getProjectDocsPath() : string
    {
        return $this->getProjectDocsRepositoryPath().$this->project->getDocsPath();
    }

    private function recursiveGlob(string $path)
    {
        $allFiles = [];

        $files =  glob($path);

        $allFiles = array_merge($allFiles, $files);

        foreach ($files as $file) {
            if (is_dir($file)) {
                $dirPath = $file.'/*';

                $dirFiles = $this->recursiveGlob($dirPath);

                $allFiles = array_merge($allFiles, $dirFiles);
            }
        }

        $allFiles = array_filter($allFiles, function(string $file) {
            return !is_dir($file);
        });

        return $allFiles;
    }

    private function shellExec(string $command)
    {
        shell_exec($command);
    }
}
