<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\StaticGenerator\SourceFile\SourceFileFilesystemReader;

final readonly class BlogPosts implements DataSource
{
    public function __construct(
        private SourceFileFilesystemReader $sourceFileFilesystemReader,
    ) {
    }

    /** @return mixed[][] */
    public function getSourceRows(): array
    {
        $sourceFiles = $this->sourceFileFilesystemReader
            ->getSourceFiles()->in('/blog/');

        $blogPosts = [];

        foreach ($sourceFiles as $sourceFile) {
            $blogPosts[] = [
                'url' => $sourceFile->getParameter('url'),
                'slug' => $sourceFile->getParameter('slug'),
                'title' => $sourceFile->getParameter('title'),
                'authorName' => (string) $sourceFile->getParameter('authorName'),
                'authorEmail' => (string) $sourceFile->getParameter('authorEmail'),
                'contents' => $sourceFile->getContents(),
                'date' => $sourceFile->getDate(),
            ];
        }

        return $blogPosts;
    }
}
