<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\Website\Builder\SourceFileRepository;
use Doctrine\Website\DataSource\DataSource;
use function array_reverse;

class BlogPosts implements DataSource
{
    /** @var SourceFileRepository */
    private $sourceFileRepository;

    public function __construct(SourceFileRepository $sourceFileRepository)
    {
        $this->sourceFileRepository = $sourceFileRepository;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows() : array
    {
        $sourceFiles = $this->sourceFileRepository->getFiles('', 'source/blog');

        $reversedSourceFiles = array_reverse($sourceFiles);

        $blogPostRows = [];

        foreach ($reversedSourceFiles as $sourceFile) {
            $blogPostRows[] = [
                'url' => $sourceFile->getParameter('url'),
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
