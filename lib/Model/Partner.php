<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Website\Repositories\PartnerRepository;

use function array_merge;
use function http_build_query;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
final class Partner
{
    /** @param array<string, scalar> $utmParameters */
    public function __construct(
        #[ORM\Column(type: 'string')]
        private string $name,
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        private string $slug,
        #[ORM\Column(type: 'string')]
        private string $url,
        #[ORM\Column(type: 'json')]
        private array $utmParameters,
        #[ORM\Column(type: 'string')]
        private string $logo,
        #[ORM\Column(type: 'text')]
        private string $bio,
        #[ORM\OneToOne(targetEntity: PartnerDetails::class, fetch: 'EAGER', orphanRemoval: true)]
        #[ORM\JoinColumn(name: 'details', referencedColumnName: 'id')]
        private PartnerDetails $details,
        #[ORM\Column(type: 'boolean')]
        private bool $featured,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /** @param string[] $parameters */
    public function getUrlWithUtmParameters(array $parameters = []): string
    {
        return $this->buildUrl($this->url, $parameters);
    }

    /** @param string[] $parameters */
    private function buildUrl(string $url, array $parameters = []): string
    {
        return $url . '?' . http_build_query(array_merge($this->utmParameters, $parameters));
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function getDetails(): PartnerDetails
    {
        return $this->details;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }
}
