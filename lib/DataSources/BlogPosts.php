<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\DataSource\DataSource;
use Doctrine\Website\DataBuilder\BlogPostDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteDataReader;

class BlogPosts implements DataSource
{
    /** @var WebsiteDataReader */
    private $dataReader;

    public function __construct(WebsiteDataReader $dataReader)
    {
        $this->dataReader = $dataReader;
    }

    /**
     * @return mixed[][]
     */
    public function getSourceRows(): array
    {
        $blogPosts = $this->dataReader
            ->read(BlogPostDataBuilder::DATA_FILE)
            ->getData();

        foreach ($blogPosts as $key => $blogPost) {
            $blogPosts[$key]['date'] = new DateTimeImmutable($blogPost['date']);
        }

        return $blogPosts;
    }
}
