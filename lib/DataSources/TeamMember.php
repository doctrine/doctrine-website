<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

use Doctrine\SkeletonMapper\DataSource\DataSource;
use Github\Client;

/** @final */
readonly class TeamMember implements DataSource
{
    public function __construct(private readonly Client $github)
    {
    }

    /** @return mixed[][] */
    public function getSourceRows(): array
    {
        $teamMembers = [];

        foreach ($this->github->team()->members('maintainers', 'doctrine') as $member) {
            $user = $this->github->user()->show($member['login']);

            $teamMembers[$user['login']] = [
                'name' => $user['name'],
                'twitter' => $user['twitter_username'],
                'website' => $user['blog'],
                'github' => $user['login'],
                'avatarUrl' => $user['avatar_url'],
                'location' => $user['location'],
                'maintains' => [],
                'headshot' => $user['avatar_url'],
                'bio' => $user['bio'],
            ];
        }

        return $teamMembers;
    }
}
