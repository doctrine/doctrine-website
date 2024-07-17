<?php

declare(strict_types=1);

namespace Doctrine\Website\Git;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Website\Model\ProjectVersion;

use function ltrim;
use function stripos;
use function strpos;
use function strtoupper;

#[ORM\Entity]
final class Tag
{
    private const ALPHA  = 'alpha';
    private const BETA   = 'beta';
    private const RC     = 'rc';
    private const DEV    = 'dev';
    private const STABLE = 'stable';

    private const TAG_STABILITIES = [
        self::ALPHA,
        self::BETA,
        self::RC,
    ];

    private const COMPOSER_EPOCH = '2011-09-25';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: ProjectVersion::class, inversedBy: 'tags')]
    private ProjectVersion $projectVersion;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private readonly string $name,
        #[ORM\Column(type: 'datetime_immutable')]
        private readonly DateTimeImmutable $date,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return strtoupper(ltrim($this->name, 'v'));
    }

    public function getDisplayName(): string
    {
        return strtoupper(ltrim($this->name, 'v'));
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getComposerRequireVersionString(): string
    {
        return ltrim($this->name, 'v');
    }

    public function isPreComposer(): bool
    {
        return $this->date < new DateTimeImmutable(self::COMPOSER_EPOCH);
    }

    public function isDev(): bool
    {
        return strpos($this->name, 'dev-') === 0;
    }

    public function isMajorReleaseZero(): bool
    {
        return strpos($this->getComposerRequireVersionString(), '0.') === 0;
    }

    public function getStability(): string
    {
        if ($this->isMajorReleaseZero()) {
            return self::DEV;
        }

        foreach (self::TAG_STABILITIES as $stability) {
            if (stripos($this->name, $stability) !== false) {
                return $stability;
            }
        }

        return self::STABLE;
    }
}
