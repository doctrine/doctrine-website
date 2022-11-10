<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Git;

use DateTimeImmutable;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Tests\TestCase;

class TagTest extends TestCase
{
    /** @var DateTimeImmutable */
    private $date;

    /** @var string */
    private $name;

    /** @var Tag */
    private $tag;

    public function testGetName(): void
    {
        self::assertSame('v1.0.0', $this->tag->getName());
    }

    public function testGetDate(): void
    {
        self::assertSame($this->date, $this->tag->getDate());
    }

    public function testGetComposerRequireVersionString(): void
    {
        self::assertSame('1.0.0', $this->tag->getComposerRequireVersionString());
    }

    public function testIsPreComposer(): void
    {
        self::assertTrue($this->tag->isPreComposer());
    }

    public function testStableStability(): void
    {
        $tag = new Tag(
            'v1.0.0',
            new DateTimeImmutable('1985-09-01'),
        );

        self::assertSame('stable', $tag->getStability());
    }

    public function testAlphaStability(): void
    {
        $tag = new Tag(
            'v1.0.0-alpha1',
            new DateTimeImmutable('1985-09-01'),
        );

        self::assertSame('alpha', $tag->getStability());
    }

    public function testBetaStability(): void
    {
        $tag = new Tag(
            'v1.0.0-beta1',
            new DateTimeImmutable('1985-09-01'),
        );

        self::assertSame('beta', $tag->getStability());
    }

    public function testRcStability(): void
    {
        $tag = new Tag(
            'v1.0.0-rc1',
            new DateTimeImmutable('1985-09-01'),
        );

        self::assertSame('rc', $tag->getStability());
    }

    public function testDevStability(): void
    {
        $tag = new Tag(
            'v0.0.1',
            new DateTimeImmutable('1985-09-01'),
        );

        self::assertSame('dev', $tag->getStability());
    }

    public function testIsMajorReleaseZeroTrue(): void
    {
        $tag = new Tag(
            'v0.0.1',
            new DateTimeImmutable('1985-09-01'),
        );

        self::assertTrue($tag->isMajorReleaseZero());
    }

    public function testIsMajorReleaseZeroFalse(): void
    {
        $tag = new Tag(
            'v1.0.1',
            new DateTimeImmutable('1985-09-01'),
        );

        self::assertFalse($tag->isMajorReleaseZero());
    }

    protected function setUp(): void
    {
        $this->date = new DateTimeImmutable('1985-09-01');
        $this->name = 'v1.0.0';

        $this->tag = new Tag(
            $this->name,
            $this->date,
        );
    }
}
