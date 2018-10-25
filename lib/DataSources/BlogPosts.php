<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileFilesystemReader;

class BlogPosts implements DataSource
{
    /** @var SourceFileFilesystemReader */
    private $sourceFileFilesystemReader;

    public function __construct(SourceFileFilesystemReader $sourceFileFilesystemReader)
    {
        $this->sourceFileFilesystemReader = $sourceFileFilesystemReader;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        $sourceFiles = $this->sourceFileFilesystemReader
            ->getSourceFiles()->in('/blog/');

        $blogPostRows = [];

        foreach ($sourceFiles as $sourceFile) {
            $blogPostRows[] = [
                'url' => $sourceFile->getParameter('url'),
                'slug' => $sourceFile->getParameter('slug'),
                'title' => $sourceFile->getParameter('title'),
                'authorName' => (string) $sourceFile->getParameter('authorName'),
                'authorEmail' => (string) $sourceFile->getParameter('authorEmail'),
                'contents' => $sourceFile->getContents(),
                'date' => $sourceFile->getDate(),
            ];
        }

        return $blogPostRows;
    }
}
