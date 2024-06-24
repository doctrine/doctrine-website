<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources\DbPrefill;

class Foo
{
    public function __construct(public int $id, public string $name)
    {
    }
}
