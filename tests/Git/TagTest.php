<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Git;

use DateTimeImmutable;
use Doctrine\Website\Git\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    /** @var DateTimeImmutable */
    private $date;

    /** @var string */
    private $name;

    /** @var Tag */
    private $tag;

    public function testGetName() : void
    {
        self::assertSame('v1.0.0', $this->tag->getName());
    }

    public function testGetDate() : void
    {
        self::assertSame($this->date, $this->tag->getDate());
    }

    public function testGetComposerRequireVersionString() : void
    {
        self::assertSame('1.0.0', $this->tag->getComposerRequireVersionString());
    }

    public function testIsPreComposer() : void
    {
        self::assertTrue($this->tag->isPreComposer());
    }

    protected function setUp() : void
    {
        $this->date = new DateTimeImmutable('1985-09-01');
        $this->name = 'v1.0.0';

        $this->tag = new Tag(
            $this->name,
            $this->date
        );
    }
}
