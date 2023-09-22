<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Hydrators;

use DateTimeImmutable;
use Doctrine\Website\Hydrators\BlogPostHydrator;
use Doctrine\Website\Model\BlogPost;

class BlogPostHydratorTest extends Hydrators
{
    public function testHydrate(): void
    {
        $hydrator = $this->createHydrator(BlogPostHydrator::class);

        $date = new DateTimeImmutable('2000-01-01');

        $expected = new BlogPost(
            'url',
            'slug',
            'title',
            'authorName',
            'authorEmail',
            'contents',
            $date,
        );

        $blogPost = new BlogPost(
            '',
            '',
            '',
            '',
            '',
            '',
            new DateTimeImmutable('1999-01-01'),
        );

        $hydrator->hydrate($blogPost, [
            'url' => 'url',
            'slug' => 'slug',
            'title' => 'title',
            'authorName' => 'authorName',
            'authorEmail' => 'authorEmail',
            'contents' => 'contents',
            'date' => $date,
        ]);

        self::assertEquals($expected, $blogPost);
    }
}
