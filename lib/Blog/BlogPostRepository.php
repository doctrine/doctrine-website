<?php

declare(strict_types=1);

namespace Doctrine\Website\Blog;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileRepository;
use InvalidArgumentException;
use function array_map;
use function array_reverse;
use function array_slice;
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
    public function findPaginated(int $page = 1, int $perPage = 10) : array
    {
        if ($page < 1 || $perPage < 1) {
            throw new InvalidArgumentException('Pagination parameters must be positive.');
        }

        $offset = ($page - 1) * $perPage;

        return array_slice($this->findAll(), $offset, $perPage);
    }

    /**
     * @return BlogPost[]
     */
    public function findAll() : array
    {
        $sourceFiles = $this->sourceFileRepository->getFiles('', 'source/blog');

        usort($sourceFiles, static function (SourceFile $a, SourceFile $b) : int {
            return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
        });

        $reversedSourceFiles = array_reverse($sourceFiles);

        return array_map(function (SourceFile $sourceFile) : BlogPost {
            return $this->find($sourceFile);
        }, $reversedSourceFiles);
    }
}
