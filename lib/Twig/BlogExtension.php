<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileRepository;
use Twig_Extension;
use Twig_SimpleFunction;
use function array_reverse;
use function usort;

class BlogExtension extends Twig_Extension
{
    /** @var SourceFileRepository */
    private $sourceFileRepository;

    public function __construct(SourceFileRepository $sourceFileRepository)
    {
        $this->sourceFileRepository = $sourceFileRepository;
    }

    /**
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('get_blog_posts', [$this, 'getBlogPosts']),
        ];
    }

    /**
     * @return SourceFile[]
     */
    public function getBlogPosts() : array
    {
        $blogPosts = $this->sourceFileRepository->getFiles('', 'source/blog');

        usort($blogPosts, function (SourceFile $a, SourceFile $b) {
            return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
        });

        return array_reverse($blogPosts);
    }
}
