<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Doctrine\RST\Builder;
use Doctrine\RST\Document;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Symfony\Component\Filesystem\Filesystem;

class RSTBuilder
{
    /** @var RSTFileRepository */
    private $rstFileRepository;

    /** @var RSTCopier */
    private $rstCopier;

    /** @var Builder */
    private $builder;

    /** @var RSTPostBuildProcessor */
    private $rstPostBuildProcessor;

    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $sourcePath;

    /** @var string */
    private $docsPath;

    public function __construct(
        RSTFileRepository $rstFileRepository,
        RSTCopier $rstCopier,
        Builder $builder,
        RSTPostBuildProcessor $rstPostBuildProcessor,
        Filesystem $filesystem,
        string $sourcePath,
        string $docsPath
    ) {
        $this->rstFileRepository     = $rstFileRepository;
        $this->rstCopier             = $rstCopier;
        $this->builder               = $builder;
        $this->rstPostBuildProcessor = $rstPostBuildProcessor;
        $this->filesystem            = $filesystem;
        $this->sourcePath            = $sourcePath;
        $this->docsPath              = $docsPath;
    }

    /**
     * @return Document[]
     */
    public function buildRSTDocs(Project $project, ProjectVersion $version, RSTLanguage $language) : array
    {
        // copy the docs from the project to a central location in $docsPath
        $this->rstCopier->copyRst($project, $version);

        // build the rst and prepare html for ./bin/console build-website
        $this->buildRst($project, $version, $language);

        // process the built html and do extra things to the html
        $this->rstPostBuildProcessor->postRstBuild($project, $version, $language);

        return $this->builder->getDocuments();
    }

    private function buildRst(Project $project, ProjectVersion $version, RSTLanguage $language) : void
    {
        $outputPath = $project->getProjectVersionDocsOutputPath($this->sourcePath, $version, $language->getCode());

        // clear the files in the output path first
        $this->filesystem->remove($this->rstFileRepository->findFiles($outputPath));

        // we have to get a fresh builder due to how the RST parser works
        $this->builder = $this->builder->recreate();

        // build the docs from the files in $docsPath and write them to $outputPath
        // which is contained inside the $sourcePath
        $this->builder->build(
            $project->getProjectVersionDocsPath($this->docsPath, $version, $language->getCode()),
            $outputPath,
            false
        );
    }
}
