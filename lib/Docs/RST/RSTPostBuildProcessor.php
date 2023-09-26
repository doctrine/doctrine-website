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

/** @final */
class RSTPostBuildProcessor
{
    final public const PARAMETERS_TEMPLATE = <<<'TEMPLATE'
        ---
        title: "%s"
        docsIndex: %s
        docsSourcePath: "%s"
        ---
        %s
        TEMPLATE;

    public function __construct(
        private readonly RSTFileRepository $rstFileRepository,
        private readonly Filesystem $filesystem,
        private readonly string $sourceDir,
    ) {
    }

    public function postRstBuild(Project $project, ProjectVersion $version, RSTLanguage $language): void
    {
        $projectVersionDocsOutputPath = $project->getProjectVersionDocsOutputPath(
            $this->sourceDir,
            $version,
            $language->getCode(),
        );

        $this->filesystem->remove($this->rstFileRepository->findMetaFiles($projectVersionDocsOutputPath));

        $files = $this->rstFileRepository->findFiles($projectVersionDocsOutputPath);

        foreach ($files as $file) {
            $this->processFile($file);
        }
    }

    private function processFile(string $file): void
    {
        $contents = $this->getFileContents($file);

        $processedContents = $this->processFileContents(
            $file,
            $contents,
        );

        $this->filesystem->dumpFile($file, $processedContents);
    }

    private function processFileContents(string $file, string $contents): string
    {
        if (strpos($file, '.html') !== false) {
            return $this->processHtmlFile($file, $contents);
        }

        return $contents;
    }

    private function processHtmlFile(
        string $file,
        string $contents,
    ): string {
        // parse out the source file that generated this file
        preg_match('/<p>{{ DOCS_SOURCE_PATH : (.*) }}<\/p>/', $contents, $match);

        $docsSourcePath = $match[1];

        // get rid of the special DOCS_SOURCE_PATH : syntax in the contents
        $contents = str_replace($match[0], '', $contents);

        $title = $this->extractTitle($contents);

        $contents = $this->fixHeaderAnchors($contents);

        $contents = str_replace('<p>SIDEBAR BEGIN</p>', '{% block sidebar %}', $contents);
        $contents = str_replace('<p>CONTENT BEGIN</p>', '{% endblock %}{% block content %}{% verbatim %}', $contents);

        $contents .= '{% endverbatim %}';
        $contents .= '{% endblock %}';

        return sprintf(
            self::PARAMETERS_TEMPLATE,
            $this->escapeYaml($title),
            strpos($file, 'index.html') !== false ? 'true' : 'false',
            $docsSourcePath,
            $contents,
        );
    }

    private function escapeYaml(string $text): string
    {
        return str_replace('\\', '\\\\', $text);
    }

    private function getFileContents(string $file): string
    {
        $contents = $this->rstFileRepository->getFileContents($file);

        // grab the html out of the <body> because that is all we need
        preg_match('/<body>(.*)<\/body>/s', $contents, $matches);

        return $matches[1] ?? $contents;
    }

    private function fixHeaderAnchors(string $contents): string
    {
        return preg_replace(
            '/<div class="section" id="(.*)">\n<h(\d)>(.*)<\/h(\d)>/',
            '<div class="section"><a class="section-anchor" id="$1" name="$1"></a><h$2 class="section-header"><a href="#$1">$3<i class="fas fa-link"></i></a></h$2>',
            $contents,
        );
    }

    private function extractTitle(string $contents): string
    {
        preg_match('/<h1>(.*)<\/h1>/', $contents, $matches);

        $title = '';

        if ($matches !== []) {
            $title = $matches[1];
        }

        return $title;
    }
}
