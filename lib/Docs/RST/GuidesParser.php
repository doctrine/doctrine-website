<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Flyfinder\Finder;
use Flyfinder\Specification\Glob;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Tactician\CommandBus;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Handlers\CompileDocumentsCommand;
use phpDocumentor\Guides\Handlers\ParseDirectoryCommand;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ProjectNode;

class GuidesParser
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    /** @return DocumentNode[] */
    public function parse(string $directory, ProjectNode|null $projectNode = null): array
    {
        $sourceFileSystem = new Filesystem(new Local($directory));
        $sourceFileSystem->addPlugin(new Finder());

        $projectNode ??= new ProjectNode();
        $documents     = $this->commandBus->handle(
            new ParseDirectoryCommand(
                $sourceFileSystem,
                '',
                'rst',
                $projectNode,
                new Glob('/**/sidebar.rst'),
            ),
        );

        return $this->commandBus->handle(
            new CompileDocumentsCommand($documents, new CompilerContext($projectNode)),
        );
    }
}
