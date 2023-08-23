<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

class RSTLanguage
{
    public function __construct(private string $code, private string $path)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
