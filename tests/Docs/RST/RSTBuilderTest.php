<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs\RST;

use Doctrine\RST\Builder\Documents;
use Doctrine\Website\Docs\RST\DocumentsBuilder;
use Doctrine\Website\Docs\RST\RSTBuilder;
use Doctrine\Website\Docs\RST\RSTCopier;
use Doctrine\Website\Docs\RST\RSTFileRepository;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;
use phpDocumentor\Guides\Nodes\DocumentNode;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Filesystem\Filesystem;

class RSTBuilderTest extends TestCase
{
    private RSTFileRepository&MockObject $rstFileRepository;

    private RSTCopier&MockObject $rstCopier;

    private DocumentsBuilder&MockObject $builder;

    private Filesystem&MockObject $filesystem;

    private string $sourceDir;

    private string $docsDir;

    private RSTBuilder $rstBuilder;

    protected function setUp(): void
    {
        $this->rstFileRepository     = $this->createMock(RSTFileRepository::class);
        $this->rstCopier             = $this->createMock(RSTCopier::class);
        $this->builder               = $this->createMock(DocumentsBuilder::class);
        $this->filesystem            = $this->createMock(Filesystem::class);
        $this->sourceDir             = '/source';
        $this->docsDir               = '/docs';

        $this->rstBuilder = new RSTBuilder(
            $this->rstFileRepository,
            $this->rstCopier,
            $this->builder,
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
            ->method('build')
            ->with(
                '/docs/docs-slug/en/version-slug',
                '/source/projects/docs-slug/en/version-slug',
            );

        $document1 = new DocumentNode('test1', 'test1');
        $document2 = new DocumentNode('test2', 'test2');

        $documentsArray = [$document1, $document2];

        $this->builder->expects(self::once())
            ->method('getDocuments')
            ->willReturn($documentsArray);

        $english = new RSTLanguage('en', '/en');

        self::assertSame(
            $documentsArray,
            $this->rstBuilder->buildRSTDocs($project, $version, $english),
        );
    }
}
