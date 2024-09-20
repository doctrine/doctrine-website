<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Flyfinder\Finder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Tactician\CommandBus;
use Override;
use phpDocumentor\Guides\Handlers\RenderCommand;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ProjectNode;

final class Guides implements DocumentsBuilder
{
    /** @var DocumentNode[] */
    private array $documents = [];

    public function __construct(
        private CommandBus $commandBus,
        private GuidesParser $guidesParser,
    ) {
    }

    #[Override]
    public function build(string $directory, string $targetDirectory = 'output'): void
    {
        $sourceFileSystem = new Filesystem(new Local($directory));
        $sourceFileSystem->addPlugin(new Finder());

        $projectNode     = new ProjectNode();
        $this->documents = $this->guidesParser->parse($directory, $projectNode);

        $destinationFileSystem = new Filesystem(new Local($targetDirectory));

        $this->commandBus->handle(
            new RenderCommand(
                'html',
                $this->documents,
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
        return $this->documents;
    }
}
