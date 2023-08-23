<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use DateTimeImmutable;
use Doctrine\Website\Model\BlogPost;

/**
 * @property string $url
 * @property string $slug
 * @property string $title
 * @property string $authorName
 * @property string $authorEmail
 * @property string $contents
 * @property DateTimeImmutable $date
 * @template-extends ModelHydrator<BlogPost>
 */
final class BlogPostHydrator extends ModelHydrator
{
    /** @return class-string<BlogPost> */
    protected function getClassName(): string
    {
        return BlogPost::class;
    }

    /** @param mixed[] $data */
    protected function doHydrate(array $data): void
    {
        $this->url         = (string) ($data['url'] ?? '');
        $this->slug        = (string) ($data['slug'] ?? '');
        $this->title       = (string) ($data['title'] ?? '');
        $this->authorName  = (string) ($data['authorName'] ?? '');
        $this->authorEmail = (string) ($data['authorEmail'] ?? '');
        $this->contents    = (string) ($data['contents'] ?? '');
        $this->date        = $data['date'] ?? new DateTimeImmutable();
    }
}
