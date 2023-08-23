<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use function array_merge;
use function http_build_query;

final class UtmParameters
{
    /** @param string[] $parameters */
    public function __construct(private array $parameters)
    {
    }

    /** @param string[] $parameters */
    public function buildUrl(string $url, array $parameters = []): string
    {
        return $url . '?' . http_build_query(array_merge($this->parameters, $parameters));
    }
}
