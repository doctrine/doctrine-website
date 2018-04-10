<?php

namespace Doctrine\Website\Docs;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Gregwar\RST\Builder;

class RSTBuilder
{
    const RST_TEMPLATE = <<<TEMPLATE
.. raw:: html
    {% block sidebar %}

.. toctree::
    :depth: 3
    :glob:

    *

.. raw:: html
    {% endblock %}


.. raw:: html
    {% block content %}

{% verbatim %}

{{ content }}

{% endverbatim %}

.. raw:: html
    {% endblock %}

TEMPLATE;

    const SCULPIN_TEMPLATE = <<<TEMPLATE
---
layout: "documentation"
indexed: true
title: "%s"
menuSlug: "projects"
docsSlug: "%s"
docsPage: true
docsIndex: "%s"
docsVersion: "%s"
sourceFile: "%s"
permalink: "none"
---
%s
TEMPLATE;

    /** @var string */
    private $sculpinSourcePath;

    /** @var Builder */
    private $builder;

    /** @var string */
    private $projectsPath;

    /** @var string */
    private $tmpPath;

    public function __construct(
        string $sculpinSourcePath,
        Builder $builder,
        string $projectsPath)
    {
        $this->sculpinSourcePath = $sculpinSourcePath;
        $this->builder = $builder;
        $this->projectsPath = $projectsPath;
        $this->tmpPath = $this->sculpinSourcePath.'/../docs';
    }

    public function getDocuments() : array
    {
        return $this->builder->getDocuments();
    }

    public function projectHasDocs(Project $project) : bool
    {
        return file_exists($this->getProjectDocsPath($project).'/en/index.rst');
    }

    public function buildRSTDocs(Project $project, ProjectVersion $version)
    {
        $this->copyRst($project, $version);

        $this->buildRst($project, $version);

        $this->postRstBuild($project, $version);
    }

    private function copyRst(Project $project, ProjectVersion $version)
    {
        $outputPath = $this->getProjectVersionTmpPath($project, $version);

        // clear tmp directory first
        shell_exec(sprintf('rm -rf %s/*', $outputPath));

        $files = $this->findFiles($this->getProjectDocsPath($project).'/en');

        foreach ($files as $file) {
            // skip non .rst files
            if (strpos($file, '.rst') === false) {
                continue;
            }

            // skip toc.rst
            if (strpos($file, 'toc.rst') !== false) {
                continue;
            }

            $path = str_replace($this->getProjectDocsPath($project).'/en/', '', $file);

            $newPath = $outputPath.'/'.$path;

            $this->ensureDirectoryExists(dirname($newPath));

            $content = trim(file_get_contents($file));

            // fix incorrect casing of note
            $content = str_replace('.. Note::', '.. note::', $content);

            // fix :maxdepth: to :depth:
            $content = str_replace(':maxdepth:', ':depth:', $content);

            // get rid of .. include:: toc.rst
            $content = str_replace('.. include:: toc.rst', '', $content);

            // stuff from doctrine1 docs
            if ($project->getSlug() === 'doctrine1') {
                $content = preg_replace("/:code:(.*)\n/", '$1', $content);
                $content = preg_replace("/:php:(.*):`(.*)`/", '$2', $content);
                $content = preg_replace("/:file:`(.*)`/", '$1', $content);
                $content = preg_replace("/:code:`(.*)`/", '$1', $content);
                $content = preg_replace("/:literal:`(.*)`/", '$1', $content);
                $content = preg_replace("/:token:`(.*)`/", '$1', $content);
                $content = str_replace('.. productionlist::', '', $content);
                $content = preg_replace("/.. rubric:: Notes/", '', $content);
                $content = preg_replace("/.. sidebar:: (.*)\n/", '$1', $content);
            }

            $newContent = str_replace('{{ content }}', $content, self::RST_TEMPLATE);

            // append the source file name to the content so we can parse it back out
            // for use in the sculpin build process
            $newContent .= sprintf('{{ SOURCE_FILE:/en/%s }}', $path);

            file_put_contents($newPath, $newContent);
        }
    }

    private function buildRst(Project $project, ProjectVersion $version)
    {
        $outputPath = $this->getProjectVersionSourcePath($project, $version);

        // clear projects docs source in the sculpin source folder before rebuilding
        shell_exec(sprintf('rm -rf %s/*', $outputPath));

        // we have to get a fresh builder due to how the RST parser works
        $this->builder = $this->builder->recreate();

        $this->builder->build(
            $this->getProjectVersionTmpPath($project, $version),
            $outputPath,
            false
        );
    }

    private function postRstBuild(Project $project, ProjectVersion $version)
    {
        $projectDocsVersionPath = $this->getProjectVersionSourcePath($project, $version);

        $files = $this->findFiles($projectDocsVersionPath);

        foreach ($files as $file) {
            // we don't want the meta.php files in the end result
            if (strpos($file, 'meta.php')) {
                unlink($file);

                continue;
            }

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
                '<a class="section-anchor" id="$1" name="$1"></a><h$2 class="section-header"><a href="#$1">$3<i class="fas fa-link"></i></a></h$2>',
                $content
            );

            // grab the html out of the <body> because that is all we need
            // sculpin will wrap it with the layout
            preg_match("/<body>(.*)<\/body>/s", $content, $matches);

            $content = isset($matches[1]) ? $matches[1] : $content;

            if (strpos($file, '.html') !== false) {
                // parse out the source file that generated this file
                preg_match('/<p>{{ SOURCE_FILE:(.*) }}<\/p>/', $content, $match);

                $sourceFile = $match[1];

                // get rid of this special syntax in the content
                $content = str_replace($match[0], '', $content);

                $newContent = sprintf(self::SCULPIN_TEMPLATE,
                    $title,
                    $project->getDocsSlug(),
                    strpos($file, 'index.html') !== false ? 'true' : 'false',
                    $version->getSlug(),
                    $sourceFile,
                    $content
                );
            } else {
                $newContent = $content;
            }

            file_put_contents($file, $newContent);
        }
    }

    private function findFiles(string $root) : array
    {
        return $this->recursiveGlob($root.'/*');
    }

    private function getProjectDocsPath(Project $project) : string
    {
        return $project->getAbsoluteDocsPath($this->projectsPath);
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

    private function ensureDirectoryExists(string $dir)
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    private function getProjectVersionSourcePath(Project $project, ProjectVersion $version) : string
    {
        return $this->sculpinSourcePath.'/projects/'.$project->getDocsSlug().'/en/'.$version->getSlug();
    }

    private function getProjectVersionTmpPath(Project $project, ProjectVersion $version) : string
    {
        return $this->tmpPath.'/'.$project->getDocsSlug().'/en/'.$version->getSlug();
    }
}
