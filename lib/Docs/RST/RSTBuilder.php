<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Doctrine\RST\Builder;
use Doctrine\RST\Nodes\DocumentNode;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Symfony\Component\Filesystem\Filesystem;

/** @final */
class RSTBuilder
{
    public function __construct(
        private readonly RSTFileRepository $rstFileRepository,
        private readonly RSTCopier $rstCopier,
        private Builder $builder,
        private readonly RSTPostBuildProcessor $rstPostBuildProcessor,
        private readonly Filesystem $filesystem,
        private readonly string $sourceDir,
        private readonly string $docsDir,
    ) {
    }

    /** @return DocumentNode[] */
    public function buildRSTDocs(Project $project, ProjectVersion $version, RSTLanguage $language): array
    {
        // copy the docs from the project to a central location in $docsDir
        $this->rstCopier->copyRst($project, $version);

        // build the rst and prepare html for ./bin/console build-website
        $this->buildRst($project, $version, $language);

        // process the built html and do extra things to the html
        $this->rstPostBuildProcessor->postRstBuild($project, $version, $language);

        return $this->builder->getDocuments()->getAll();
    }

    private function buildRst(Project $project, ProjectVersion $version, RSTLanguage $language): void
    {
        $outputPath = $project->getProjectVersionDocsOutputPath($this->sourceDir, $version, $language->getCode());

        // clear the files in the output path first
        $this->filesystem->remove($this->rstFileRepository->findFiles($outputPath));

        // we have to get a fresh builder due to how the RST parser works
        $this->builder = $this->builder->recreate();

        // build the docs from the files in $docsDir and write them to $outputPath
        // which is contained inside the $sourceDir
        $this->builder->build(
            $project->getProjectVersionDocsPath($this->docsDir, $version, $language->getCode()),
            $outputPath,
        );
    }
}
