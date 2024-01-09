<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use Doctrine\Website\DataSources\TeamMember;
use Github\Api\Organization\Teams;
use Github\Api\User;
use Github\Client;
use PHPUnit\Framework\TestCase;

class TeamMemberTest extends TestCase
{
    public function testGetSourceRows(): void
    {
        $teams = $this->createMock(Teams::class);
        $teams->method('members')
            ->with('maintainers', 'doctrine')
            ->willReturn([
                ['login' => 'foo'],
                ['login' => 'bar'],
            ]);

        $user = $this->createMock(User::class);
        $user->expects(self::exactly(2))
            ->method('show')
            ->willReturnMap([
                [
                    'foo',
                    [
                        'login' => 'foo',
                        'name' => 'John Doe',
                        'twitter_username' => 'jdoe',
                        'blog' => 'http://foo.foo',
                        'avatar_url' => 'foo.jpg',
                        'location' => 'The Internet',
                        'bio' => 'I do stuff',
                    ],
                ],
                [
                    'bar',
                    [
                        'login' => 'bar',
                        'name' => 'Jane Doe',
                        'twitter_username' => 'janed',
                        'blog' => 'http://bar.bar',
                        'avatar_url' => 'bar.jpg',
                        'location' => 'The World',
                        'bio' => 'I do more stuff',
                    ],
                ],
            ]);

        $github = $this->getMockBuilder(Client::class)->addMethods([
            'team',
            'user',
        ])->getMock();
        $github->method('team')
            ->willReturn($teams);
        $github->method('user')
            ->willReturn($user);

        $teamMember = new TeamMember($github);

        $expected = [
            'foo' => [
                'name' => 'John Doe',
                'twitter' => 'jdoe',
                'website' => 'http://foo.foo',
                'github' => 'foo',
                'avatarUrl' => 'foo.jpg',
                'location' => 'The Internet',
                'maintains' => [],
                'headshot' => 'foo.jpg',
                'bio' => 'I do stuff',
            ],
            'bar' => [
                'name' => 'Jane Doe',
                'twitter' => 'janed',
                'website' => 'http://bar.bar',
                'github' => 'bar',
                'avatarUrl' => 'bar.jpg',
                'location' => 'The World',
                'maintains' => [],
                'headshot' => 'bar.jpg',
                'bio' => 'I do more stuff',
            ],
        ];

        self::assertSame($expected, $teamMember->getSourceRows());
    }
}
