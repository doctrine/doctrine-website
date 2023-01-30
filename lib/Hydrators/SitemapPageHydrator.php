<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use DateTimeImmutable;
use Doctrine\Website\Model\SitemapPage;

/**
 * @property string $url
 * @property DateTimeImmutable $date
 * @template-extends ModelHydrator<SitemapPage>
 */
final class SitemapPageHydrator extends ModelHydrator
{
    /** @return class-string<SitemapPage> */
    protected function getClassName(): string
    {
        return SitemapPage::class;
    }

    /** @param mixed[] $data */
    protected function doHydrate(array $data): void
    {
        $this->url  = (string) ($data['url'] ?? '');
        $this->date = $data['date'] ?? new DateTimeImmutable();
    }
}
