<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Symfony\Component\Filesystem\Filesystem;
use function preg_match;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strpos;

class RSTPostBuildProcessor
{
    public const PARAMETERS_TEMPLATE = <<<TEMPLATE
---
layout: "documentation"
indexed: true
title: "%s"
menuSlug: "projects"
docsSlug: "%s"
docsPage: true
docsIndex: %s
docsVersion: "%s"
sourceFile: "%s"
lanuage: "%s"
permalink: "none"
controller: ['Doctrine\Website\Controllers\DocumentationController', 'view']
---
%s
TEMPLATE;

    /** @var RSTFileRepository */
    private $rstFileRepository;

    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $sourcePath;

    public function __construct(
        RSTFileRepository $rstFileRepository,
        Filesystem $filesystem,
        string $sourcePath
    ) {
        $this->rstFileRepository = $rstFileRepository;
        $this->filesystem        = $filesystem;
        $this->sourcePath        = $sourcePath;
    }

    public function postRstBuild(Project $project, ProjectVersion $version, RSTLanguage $language) : void
    {
        $projectVersionDocsOutputPath = $project->getProjectVersionDocsOutputPath(
            $this->sourcePath,
            $version,
            $language->getCode()
        );

        $this->filesystem->remove($this->rstFileRepository->findMetaFiles($projectVersionDocsOutputPath));

        $files = $this->rstFileRepository->findFiles($projectVersionDocsOutputPath);

        foreach ($files as $file) {
            $this->processFile($project, $version, $language, $file);
        }
    }

    private function processFile(
        Project $project,
        ProjectVersion $version,
        RSTLanguage $language,
        string $file
    ) : void {
        $contents = $this->getFileContents($file);

        $processedContents = $this->processFileContents(
            $project,
            $version,
            $language,
            $file,
            $contents
        );

        $this->filesystem->dumpFile($file, $processedContents);
    }

    private function processFileContents(
        Project $project,
        ProjectVersion $version,
        RSTLanguage $language,
        string $file,
        string $contents
    ) : string {
        if (strpos($file, '.html') !== false) {
            return $this->processHtmlFile($project, $version, $language, $file, $contents);
        }

        return $contents;
    }

    private function processHtmlFile(
        Project $project,
        ProjectVersion $version,
        RSTLanguage $language,
        string $file,
        string $contents
    ) : string {
        // parse out the source file that generated this file
        preg_match('/<p>{{ SOURCE_FILE:(.*) }}<\/p>/', $contents, $match);

        $sourceFile = $match[1];

        // get rid of the special SOURCE_FILE: syntax in the contents
        $contents = str_replace($match[0], '', $contents);

        $title = $this->extractTitle($contents);

        $contents = $this->fixHeaderAnchors($contents);

        return sprintf(
            self::PARAMETERS_TEMPLATE,
            $title,
            $project->getDocsSlug(),
            strpos($file, 'index.html') !== false ? 'true' : 'false',
            $version->getSlug(),
            $sourceFile,
            $language->getCode(),
            $contents
        );
    }

    private function getFileContents(string $file) : string
    {
        $contents = $this->rstFileRepository->getFileContents($file);

        // grab the html out of the <body> because that is all we need
        preg_match('/<body>(.*)<\/body>/s', $contents, $matches);

        return $matches[1] ?? $contents;
    }

    private function fixHeaderAnchors(string $contents) : string
    {
        return preg_replace(
            '/<a id="(.*)"><\/a><h(\d)>(.*)<\/h(\d)>/',
            '<a class="section-anchor" id="$1" name="$1"></a><h$2 class="section-header"><a href="#$1">$3<i class="fas fa-link"></i></a></h$2>',
            $contents
        );
    }

    private function extractTitle(string $contents) : string
    {
        preg_match('/<h1>(.*)<\/h1>/', $contents, $matches);

        $title = '';

        if ($matches !== []) {
            $title = $matches[1];
        }

        return $title;
    }
}
