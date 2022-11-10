<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs\RST;

use Doctrine\RST\Builder;
use Doctrine\RST\Builder\Documents;
use Doctrine\RST\Nodes\DocumentNode;
use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Docs\RST\RSTCopier;
use Doctrine\Website\Docs\RST\RSTFileRepository;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Docs\RST\RSTPostBuildProcessor;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Filesystem\Filesystem;

class RSTBuilderTest extends TestCase
{
    /** @var RSTFileRepository|MockObject */
    private $rstFileRepository;

    /** @var RSTCopier|MockObject */
    private $rstCopier;

    /** @var Builder|MockObject */
    private $builder;

    /** @var RSTPostBuildProcessor|MockObject */
    private $rstPostBuildProcessor;

    /** @var Filesystem|MockObject */
    private $filesystem;

    /** @var string */
    private $sourceDir;

    /** @var string */
    private $docsDir;

    /** @var RSTBuilder */
    private $rstBuilder;

    protected function setUp(): void
    {
        $this->rstFileRepository     = $this->createMock(RSTFileRepository::class);
        $this->rstCopier             = $this->createMock(RSTCopier::class);
        $this->builder               = $this->createMock(Builder::class);
        $this->rstPostBuildProcessor = $this->createMock(RSTPostBuildProcessor::class);
        $this->filesystem            = $this->createMock(Filesystem::class);
        $this->sourceDir             = '/source';
        $this->docsDir               = '/docs';

        $this->rstBuilder = new RSTBuilder(
            $this->rstFileRepository,
            $this->rstCopier,
            $this->builder,
            $this->rstPostBuildProcessor,
            $this->filesystem,
            $this->sourceDir,
            $this->docsDir,
        );
    }

    public function testBuildRSTDocs(): void
    {
        $project = $this->createProject([
            'slug' => 'project-slug',
            'docsSlug' => 'docs-slug',
        ]);
        $version = new ProjectVersion(['slug' => 'version-slug']);

        $this->rstCopier->expects(self::once())
            ->method('copyRst')
            ->with($project, $version);

        $this->rstFileRepository->expects(self::once())
            ->method('findFiles')
            ->with('/source/projects/docs-slug/en/version-slug')
            ->willReturn(['/test1', '/test2']);

        $this->filesystem->expects(self::once())
            ->method('remove')
            ->with(['/test1', '/test2']);

        $this->builder->expects(self::once())
            ->method('recreate')
            ->willReturn($this->builder);

        $this->builder->expects(self::once())
            ->method('build')
            ->with(
                '/docs/docs-slug/en/version-slug',
                '/source/projects/docs-slug/en/version-slug',
            );

        $this->rstPostBuildProcessor->expects(self::once())
            ->method('postRstBuild')
            ->with($project, $version);

        $document1 = $this->createMock(DocumentNode::class);
        $document2 = $this->createMock(DocumentNode::class);

        $documentsArray = [$document1, $document2];
        $documents      = $this->createMock(Documents::class);

        $documents->expects(self::once())
            ->method('getAll')
            ->willReturn($documentsArray);

        $this->builder->expects(self::once())
            ->method('getDocuments')
            ->willReturn($documents);

        $english = new RSTLanguage('en', '/en');

        self::assertSame(
            $documentsArray,
            $this->rstBuilder->buildRSTDocs($project, $version, $english),
        );
    }
}
