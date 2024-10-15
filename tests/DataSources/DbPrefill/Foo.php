<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources\DbPrefill;

class Foo
{
    public function __construct(public int $id, public string $name)
    {
    }

    public function equals(Foo $foo): bool
    {
        return $foo->id === $this->id && $foo->name === $this->name;
    }
}
