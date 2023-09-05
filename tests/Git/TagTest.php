<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Git;

use DateTimeImmutable;
use Doctrine\Website\Git\Tag;
use Doctrine\Website\Tests\TestCase;

class TagTest extends TestCase
{
    public function testGetName(): void
    {
        $tag = new Tag(
            'v1.0.0',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('v1.0.0', $tag->getName());
    }

    public function testGetDate(): void
    {
        $date = $this->createDateTimeImmtable();
        $tag  = new Tag(
            'v1.0.0',
            $date,
        );

        self::assertSame($date, $tag->getDate());
    }

    public function testGetComposerRequireVersionString(): void
    {
        $tag = new Tag(
            'v1.0.0',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('1.0.0', $tag->getComposerRequireVersionString());
    }

    public function testIsPreComposer(): void
    {
        $tag = new Tag(
            'v1.0.0',
            $this->createDateTimeImmtable(),
        );

        self::assertTrue($tag->isPreComposer());
    }

    public function testStableStability(): void
    {
        $tag = new Tag(
            'v1.0.0',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('stable', $tag->getStability());
    }

    public function testAlphaStability(): void
    {
        $tag = new Tag(
            'v1.0.0-alpha1',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('alpha', $tag->getStability());
    }

    public function testBetaStability(): void
    {
        $tag = new Tag(
            'v1.0.0-beta1',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('beta', $tag->getStability());
    }

    public function testRcStability(): void
    {
        $tag = new Tag(
            'v1.0.0-rc1',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('rc', $tag->getStability());
    }

    public function testDevStability(): void
    {
        $tag = new Tag(
            'v0.0.1',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('dev', $tag->getStability());
    }

    public function testIsMajorReleaseZeroTrue(): void
    {
        $tag = new Tag(
            'v0.0.1',
            $this->createDateTimeImmtable(),
        );

        self::assertTrue($tag->isMajorReleaseZero());
    }

    public function testIsMajorReleaseZeroFalse(): void
    {
        $tag = new Tag(
            'v1.0.1',
            $this->createDateTimeImmtable(),
        );

        self::assertFalse($tag->isMajorReleaseZero());
    }

    public function testGetSlug(): void
    {
        $tag = new Tag(
            'v1.0.0',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('1.0.0', $tag->getSlug());
    }

    public function testGetDisplayName(): void
    {
        $tag = new Tag(
            'v1.0.0',
            $this->createDateTimeImmtable(),
        );

        self::assertSame('1.0.0', $tag->getDisplayName());
    }

    private function createDateTimeImmtable(): DateTimeImmutable
    {
        return new DateTimeImmutable('1985-09-01');
    }
}
