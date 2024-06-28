<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Website\Repositories\SitemapPageRepository;

#[ORM\Entity(repositoryClass: SitemapPageRepository::class)]
class SitemapPage
{
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        private string $url,
        DateTimeImmutable|null $date = null,
    ) {
        if ($date !== null) {
            $this->date = $date;

            return;
        }

        $this->date = new DateTimeImmutable();
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
