<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

class RSTLanguage
{
    /** @var string */
    private $code;

    /** @var string */
    private $path;

    public function __construct(string $code, string $path)
    {
        $this->code = $code;
        $this->path = $path;
    }

    public function getCode() : string
    {
        return $this->code;
    }

    public function getPath() : string
    {
        return $this->path;
    }
}
