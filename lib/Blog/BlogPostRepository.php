<?php

declare(strict_types=1);

namespace Doctrine\Website\Blog;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileRepository;
use function array_map;
use function array_reverse;
use function usort;

class BlogPostRepository
{
    /** @var SourceFileRepository */
    private $sourceFileRepository;

    public function __construct(SourceFileRepository $sourceFileRepository)
    {
        $this->sourceFileRepository = $sourceFileRepository;
    }

    public function find(SourceFile $sourceFile) : BlogPost
    {
        return new BlogPost(
            $sourceFile->getParameter('url'),
            $sourceFile->getParameter('title'),
            (string) $sourceFile->getParameter('authorName'),
            (string) $sourceFile->getParameter('authorEmail'),
            $sourceFile->getContents(),
            $sourceFile->getDate()
        );
    }

    /**
     * @return BlogPost[]
     */
    public function findAll() : array
    {
        $sourceFiles = $this->sourceFileRepository->getFiles('', 'source/blog');

        usort($sourceFiles, function (SourceFile $a, SourceFile $b) : int {
            return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
        });

        $reversedSourceFiles = array_reverse($sourceFiles);

        return array_map(function (SourceFile $sourceFile) : BlogPost {
            return $this->find($sourceFile);
        }, $reversedSourceFiles);
    }
}
