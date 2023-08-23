<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use DateTimeImmutable;
use Doctrine\Website\Docs\RST\RSTLanguage;
use Doctrine\Website\Git\Tag;
use InvalidArgumentException;

use function array_map;
use function array_merge;
use function count;
use function end;
use function sprintf;

class ProjectVersion
{
    private const UPCOMING     = 'upcoming';
    private const STABLE       = 'stable';
    private const UNMAINTAINED = 'unmaintained';

    private string $name;

    private string|null $branchName;

    private string $slug;

    private bool $current = false;

    private bool $maintained = true;

    private bool $upcoming = false;

    private bool $hasDocs = true;

    /** @var RSTLanguage[] */
    private array $docsLanguages = [];

    /** @var string[] */
    private array $aliases;

    /** @var Tag[] */
    private array $tags;

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

        $this->docsLanguages = array_map(static function (array $language): RSTLanguage {
            return new RSTLanguage($language['code'], $language['path']);
        }, $version['docsLanguages'] ?? []);

        $this->tags = array_map(static function (array $tag): Tag {
            return new Tag($tag['name'], new DateTimeImmutable($tag['date']));
        }, $version['tags'] ?? []);

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
        return $this->docsLanguages;
    }

    /** @return string[] */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /** @return Tag[] */
    public function getTags(): array
    {
        return $this->tags;
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
