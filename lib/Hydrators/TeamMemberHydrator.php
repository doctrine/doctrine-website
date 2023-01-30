<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use Closure;
use Doctrine\Website\Model\Contributor;
use Doctrine\Website\Model\TeamMember;
use Doctrine\Website\Repositories\ContributorRepository;

use function assert;

/**
 * @property string $name
 * @property string $github
 * @property string $twitter
 * @property string $avatarUrl
 * @property string $website
 * @property string $location
 * @property string[] $maintains
 * @property bool $consultant
 * @property string $headshot
 * @property string $bio
 * @property Closure|Contributor $contributor
 * @template-extends ModelHydrator<TeamMember>
 */
final class TeamMemberHydrator extends ModelHydrator
{
    /** @return class-string<TeamMember> */
    protected function getClassName(): string
    {
        return TeamMember::class;
    }

    /** @param mixed[] $data */
    protected function doHydrate(array $data): void
    {
        $this->name        = (string) ($data['name'] ?? '');
        $this->github      = (string) ($data['github'] ?? '');
        $this->twitter     = (string) ($data['twitter'] ?? '');
        $this->avatarUrl   = (string) ($data['avatarUrl'] ?? '');
        $this->website     = (string) ($data['website'] ?? '');
        $this->location    = (string) ($data['location'] ?? '');
        $this->maintains   = $data['maintains'] ?? [];
        $this->consultant  = (bool) ($data['consultant'] ?? false);
        $this->headshot    = (string) ($data['headshot'] ?? '');
        $this->bio         = (string) ($data['bio'] ?? '');
        $this->contributor = function (string $github): Contributor {
            $contributorRepository = $this->objectManager
                ->getRepository(Contributor::class);

            assert($contributorRepository instanceof ContributorRepository);

            return $contributorRepository->findOneByGithub($github);
        };
    }
}
