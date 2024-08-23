<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Flyfinder\Finder;
use Flyfinder\Specification\Glob;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Tactician\CommandBus;
use Override;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Handlers\CompileDocumentsCommand;
use phpDocumentor\Guides\Handlers\ParseDirectoryCommand;
use phpDocumentor\Guides\Handlers\RenderCommand;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ProjectNode;
use phpDocumentor\Guides\Twig\Theme\ThemeManager;

final readonly class Guides implements DocumentsBuilder
{
    public function __construct(
        private CommandBus $commandBus,
        private ThemeManager $themeManager,
    ) {
    }

    #[Override]
    public function build(string $directory, string $targetDirectory = 'output'): void
    {
        $sourceFileSystem = new Filesystem(new Local($directory));
        $sourceFileSystem->addPlugin(new Finder());

        $projectNode = new ProjectNode();
        $documents   = $this->commandBus->handle(
            new ParseDirectoryCommand(
                $sourceFileSystem,
                '',
                'rst',
                $projectNode,
                new Glob('/**/sidebar.rst'),
            ),
        );

        $documents = $this->commandBus->handle(
            new CompileDocumentsCommand($documents, new CompilerContext($projectNode)),
        );

        $destinationFileSystem = new Filesystem(new Local($targetDirectory));
        $this->themeManager->useTheme('doctrine');

        $this->commandBus->handle(
            new RenderCommand(
                'html',
                $documents,
                $sourceFileSystem,
                $destinationFileSystem,
                $projectNode,
            ),
        );
    }

    /** @return DocumentNode[] */
    #[Override]
    public function getDocuments(): array
    {
        return [];
    }
}
