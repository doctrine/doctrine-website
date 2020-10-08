<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Command;

use Doctrine\StaticWebsiteGenerator\Routing\Router;
use Doctrine\Website\Commands\AnnounceReleaseCommand;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Release\AnnounceRelease;
use Doctrine\Website\Repositories\ProjectRepository;
use Doctrine\Website\Tests\TestCase;
use Doctrine\Website\Twitter\CreateTweet;
use Doctrine\Website\Twitter\TweetRelease;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

use function trim;

final class AnnounceReleaseCommandTest extends TestCase
{
    /** @var Project */
    private $project;

    /** @var CreateTweet|MockObject */
    private $createTweet;

    /** @var Router|MockObject */
    private $router;

    /** @var ProjectRepository|MockObject */
    private $projectRepository;

    /** @var TweetRelease */
    private $tweetRelease;

    /** @var AnnounceRelease */
    private $announceRelease;

    /** @var AnnounceReleaseCommand */
    private $command;

    /** @var CommandTester */
    private $commandTester;

    public function testExecuteSuccess(): void
    {
        $this->projectRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('orm')
            ->willReturn($this->project);

        $this->router->expects(self::once())
            ->method('generate')
            ->with('project_version', [
                'slug' => 'orm',
                'versionSlug' => '3.0',
            ])
            ->willReturn('http://www.url.com');

        $this->createTweet->expects(self::once())
            ->method('__invoke')
            ->with('Released Doctrine ORM 3.0.0 http://www.url.com')
            ->willReturn(true);

        self::assertSame(0, $this->commandTester->execute([
            'project' => 'orm',
            'tag' => '3.0.0',
        ]));

        self::assertEquals('Successfully announced release!', trim($this->commandTester->getDisplay()));
    }

    public function testExecuteFailure(): void
    {
        $this->projectRepository->expects(self::once())
            ->method('findOneBySlug')
            ->with('orm')
            ->willReturn($this->project);

        $this->router->expects(self::once())
            ->method('generate')
            ->with('project_version', [
                'slug' => 'orm',
                'versionSlug' => '3.0',
            ])
            ->willReturn('http://www.url.com');

        $this->createTweet->expects(self::once())
            ->method('__invoke')
            ->with('Released Doctrine ORM 3.0.0 http://www.url.com')
            ->will(self::throwException(new RuntimeException('test')));

        self::assertSame(1, $this->commandTester->execute([
            'project' => 'orm',
            'tag' => '3.0.0',
        ]));

        self::assertEquals(
            'Failed to announce release! Failed with error: test',
            trim($this->commandTester->getDisplay())
        );
    }

    protected function setUp(): void
    {
        $this->project = $this->createProject([
            'slug' => 'orm',
            'shortName' => 'ORM',
            'versions' => [
                [
                    'slug' => '3.0',
                    'tags' => [
                        [
                            'name' => 'v3.0.0',
                            'date' => '2019-09-01',
                        ],
                    ],
                ],
            ],
        ]);

        $this->createTweet       = $this->createMock(CreateTweet::class);
        $this->router            = $this->createMock(Router::class);
        $this->projectRepository = $this->createMock(ProjectRepository::class);

        $this->tweetRelease    = new TweetRelease($this->createTweet, $this->router);
        $this->announceRelease = new AnnounceRelease($this->projectRepository, $this->tweetRelease);
        $this->command         = new AnnounceReleaseCommand($this->announceRelease);
        $this->commandTester   = new CommandTester($this->command);
    }
}
