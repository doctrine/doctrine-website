<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

use Doctrine\StaticWebsiteGenerator\SourceFile\SourceFileFilesystemReader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('doctrine.website.data_builder', ['priority' => 70])]
final readonly class BlogPostDataBuilder implements DataBuilder
{
    public const DATA_FILE = 'blog_posts';

    public function __construct(
        private SourceFileFilesystemReader $sourceFileFilesystemReader,
    ) {
    }

    public function getName(): string
    {
        return self::DATA_FILE;
    }

    public function build(): WebsiteData
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
                'date' => $sourceFile->getDate()->format('Y-m-d H:i:s'),
            ];
        }

        return new WebsiteData(self::DATA_FILE, $blogPosts);
    }
}
