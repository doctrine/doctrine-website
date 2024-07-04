<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Git\Tag;
use InvalidArgumentException;

use function array_map;
use function array_merge;
use function count;
use function end;
use function sprintf;

#[ORM\Entity]
class ProjectVersion
{
    private const UPCOMING     = 'upcoming';
    private const STABLE       = 'stable';
    private const UNMAINTAINED = 'unmaintained';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'versions')]
    #[ORM\JoinColumn(referencedColumnName: 'slug')]
    private Project $project;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private string|null $branchName;

    #[ORM\Column(type: 'string')]
    private string $slug;

    #[ORM\Column(type: 'boolean')]
    private bool $current = false;

    #[ORM\Column(type: 'boolean')]
    private bool $maintained = true;

    #[ORM\Column(type: 'boolean')]
    private bool $upcoming = false;

    #[ORM\Column(type: 'boolean')]
    private bool $hasDocs = true;

    /** @var Collection<int, RSTLanguage> */
    #[ORM\OneToMany(targetEntity: RSTLanguage::class, fetch: 'EAGER', mappedBy: 'projectVersion')]
    private Collection $docsLanguages;

    /** @var string[] */
    #[ORM\Column(type: 'simple_array')]
    private array $aliases;

    /** @var Collection<int, Tag> */
    #[ORM\OneToMany(targetEntity: Tag::class, fetch: 'EAGER', mappedBy: 'projectVersion')]
    private Collection $tags;

    /** @param mixed[] $version */
    public function __construct(array $version)
    {
        $this->name       = (string) ($version['name'] ?? '');
        $this->branchName = $version['branchName'] ?? null;
        $this->slug       = (string) ($version['slug'] ?? $this->name);
        $this->current    = (bool) ($version['current'] ?? false);
        $this->maintained = (bool) ($version['maintained'] ?? true);
        $this->upcoming   = (bool) ($version['upcoming'] ?? false);
        $this->hasDocs    = (bool) ($version['hasDocs'] ?? true);

        $this->docsLanguages = new ArrayCollection(array_map(static function (array $language): RSTLanguage {
            return new RSTLanguage($language['code'], $language['path']);
        }, $version['docsLanguages'] ?? []));

        $this->tags = new ArrayCollection(array_map(static function (array $tag): Tag {
            return new Tag($tag['name'], new DateTimeImmutable($tag['date']));
        }, $version['tags'] ?? []));

        $this->aliases = $version['aliases'] ?? [];

        if (! $this->current) {
            return;
        }

        $this->aliases = array_merge($this->aliases, ['current', 'stable']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        $latestTag = $this->getLatestTag();

        if ($latestTag !== null) {
            return $latestTag->getDisplayName();
        }

        return $this->name;
    }

    public function getBranchName(): string|null
    {
        return $this->branchName;
    }

    /** @phpstan-assert-if-true !null $this->getBranchName() */
    public function hasBranchName(): bool
    {
        return $this->branchName !== null;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isCurrent(): bool
    {
        return $this->current;
    }

    public function isMaintained(): bool
    {
        return $this->maintained;
    }

    public function isUpcoming(): bool
    {
        return $this->upcoming;
    }

    public function hasDocs(): bool
    {
        return $this->hasDocs;
    }

    /** @return RSTLanguage[] */
    public function getDocsLanguages(): array
    {
        return $this->docsLanguages->getValues();
    }

    /** @return string[] */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /** @return Tag[] */
    public function getTags(): array
    {
        return $this->tags->getValues();
    }

    public function getTag(string $slug): Tag
    {
        foreach ($this->tags as $tag) {
            if ($tag->getSlug() === $slug) {
                return $tag;
            }
        }

        throw new InvalidArgumentException(sprintf('Could not find tag "%s".', $slug));
    }

    public function getFirstTag(): Tag|null
    {
        return $this->tags[0] ?? null;
    }

    public function getLatestTag(): Tag|null
    {
        $latestTag = end($this->tags);

        if ($latestTag === false) {
            return null;
        }

        return $latestTag;
    }

    /**
     * @phpstan-assert-if-true !null $this->getLatestTag()
     * @phpstan-assert-if-true !null $this->getFirstTag()
     * @phpstan-assert-if-true !null $this->getTags()
     */
    public function hasTags(): bool
    {
        return count($this->tags) > 0;
    }

    public function getStability(): string
    {
        if ($this->maintained === false) {
            return self::UNMAINTAINED;
        }

        $latestTag = $this->getLatestTag();

        if ($latestTag !== null) {
            return $latestTag->getStability();
        }

        if ($this->current === true) {
            return self::STABLE;
        }

        return self::UPCOMING;
    }

    public function getStabilityColor(string|null $stability = null): string
    {
        $map = [
            'upcoming' => 'warning',
            'alpha' => 'warning',
            'beta' => 'warning',
            'rc' => 'warning',
            'stable' => 'primary',
            'unmaintained' => 'danger',
            'dev' => 'primary',
        ];

        $stability ??= $this->getStability();

        return $map[$stability] ?? 'secondary';
    }
}
