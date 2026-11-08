<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Projects;

use DateTimeImmutable;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Projects\ProjectVersionDeriver;
use Doctrine\Website\Tests\TestCase;

use function array_map;
use function array_values;

class ProjectVersionDeriverTest extends TestCase
{
    private ProjectVersionDeriver $deriver;

    public function testDerivesCurrentUpcomingAndLatest(): void
    {
        $versions = $this->deriver->derive(
            [
                $this->gitVersion('1.0', '1.0.0'),
                $this->gitVersion('1.1', '1.1.0'),
                $this->gitVersion('1.2', '1.2.0-BETA1'),
            ],
            [
                ['name' => '1.0'],
                ['name' => '1.1'],
                ['name' => '1.2'],
            ],
        );

        self::assertSame([
            $this->summary('1.2', 'latest', false, true, true, false),
            $this->summary('1.1', '1.1', true, false, true, false),
            $this->summary('1.0', '1.0', false, false, false, false),
        ], $this->summarize($versions));
    }

    public function testDerivesUpcomingFromTheNextMinorBranch(): void
    {
        $versions = $this->deriver->derive(
            [
                $this->gitVersion('6.3', '6.3.0'),
                $this->gitVersion('6.4', '6.4.0-BETA1'),
            ],
            [
                ['name' => '6.3'],
                ['name' => '6.4'],
            ],
        );

        self::assertSame([
            $this->summary('6.4', 'latest', false, true, true, false),
            $this->summary('6.3', '6.3', true, false, true, false),
        ], $this->summarize($versions));
    }

    public function testOlderVersionStaysMaintainedOnlyWhenOverridden(): void
    {
        $versions = $this->deriver->derive(
            [
                $this->gitVersion('2.13', '2.13.0'),
                $this->gitVersion('3.0', '3.0.0'),
            ],
            [
                ['name' => '2.13', 'maintained' => true],
                ['name' => '3.0'],
            ],
        );

        self::assertSame([
            $this->summary('3.0', 'latest', true, false, true, false),
            $this->summary('2.13', '2.13', false, false, true, false),
        ], $this->summarize($versions));
    }

    public function testLifeSupportOverridesCurrentAndUpcoming(): void
    {
        $versions = $this->deriver->derive(
            [
                $this->gitVersion('2.20', '2.20.0'),
                $this->gitVersion('3.7', '3.7.0'),
                $this->gitVersion('4.0', '4.0.0-BETA1'),
            ],
            [
                ['name' => '2.20', 'lifeSupport' => true],
                ['name' => '3.7'],
                ['name' => '4.0'],
            ],
        );

        self::assertSame([
            $this->summary('4.0', 'latest', false, true, true, false),
            $this->summary('3.7', '3.7', true, false, true, false),
            $this->summary('2.20', '2.20', false, false, true, true),
        ], $this->summarize($versions));
    }

    public function testExplicitLatestSlugWins(): void
    {
        $versions = $this->deriver->derive(
            [
                $this->gitVersion('3.7', '3.7.0'),
                $this->gitVersion('3.8', '3.8.0-BETA1'),
            ],
            [
                ['name' => '3.7'],
                ['name' => '3.8', 'slug' => 'latest'],
            ],
        );

        self::assertSame([
            $this->summary('3.8', 'latest', false, true, true, false),
            $this->summary('3.7', '3.7', true, false, true, false),
        ], $this->summarize($versions));
    }

    public function testExplicitCurrentWinsOverDerivedCurrent(): void
    {
        $versions = $this->deriver->derive(
            [
                $this->gitVersion('2.20', '2.20.0'),
                $this->gitVersion('3.7', '3.7.0'),
            ],
            [
                ['name' => '2.20', 'current' => true],
                ['name' => '3.7'],
            ],
        );

        self::assertSame([
            $this->summary('3.7', 'latest', false, false, false, false),
            $this->summary('2.20', '2.20', true, false, true, false),
        ], $this->summarize($versions));
    }

    public function testVersionMissingFromGitIsKept(): void
    {
        $versions = $this->deriver->derive(
            [],
            [
                ['name' => 'master', 'branchName' => 'master', 'aliases' => ['current', 'stable']],
            ],
        );

        self::assertSame([
            [
                'name' => 'master',
                'slug' => 'latest',
                'branchName' => 'master',
                'current' => false,
                'upcoming' => false,
                'maintained' => true,
                'lifeSupport' => false,
                'aliases' => ['current', 'stable'],
            ],
        ], $versions);
    }

    public function testNoStableReleaseMeansNoCurrentVersion(): void
    {
        $versions = $this->deriver->derive(
            [
                $this->gitVersion('5.0', '5.0.0-BETA1'),
                $this->gitVersion('5.1', '5.1.0-BETA1'),
            ],
            [
                ['name' => '5.0'],
                ['name' => '5.1'],
            ],
        );

        self::assertSame([
            $this->summary('5.1', 'latest', false, false, false, false),
            $this->summary('5.0', '5.0', false, false, false, false),
        ], $this->summarize($versions));
    }

    /** @return array{name: string, slug: string, branchName: string, tags: list<Tag>} */
    private function gitVersion(string $name, string ...$tags): array
    {
        return [
            'name' => $name,
            'slug' => $name,
            'branchName' => $name . '.x',
            'tags' => array_values(array_map(
                static fn (string $tag): Tag => new Tag($tag, new DateTimeImmutable()),
                $tags,
            )),
        ];
    }

    /**
     * @param list<array<string, mixed>> $versions
     *
     * @return list<array<string, mixed>>
     */
    private function summarize(array $versions): array
    {
        return array_map(static fn (array $version): array => [
            'name' => $version['name'],
            'slug' => $version['slug'],
            'current' => $version['current'],
            'upcoming' => $version['upcoming'],
            'maintained' => $version['maintained'],
            'lifeSupport' => $version['lifeSupport'],
        ], $versions);
    }

    /** @return array<string, mixed> */
    private function summary(
        string $name,
        string $slug,
        bool $current,
        bool $upcoming,
        bool $maintained,
        bool $lifeSupport,
    ): array {
        return [
            'name' => $name,
            'slug' => $slug,
            'current' => $current,
            'upcoming' => $upcoming,
            'maintained' => $maintained,
            'lifeSupport' => $lifeSupport,
        ];
    }

    protected function setUp(): void
    {
        $this->deriver = new ProjectVersionDeriver();
    }
}
