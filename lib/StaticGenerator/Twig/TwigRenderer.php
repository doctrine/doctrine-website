<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Twig;

interface TwigRenderer
{
    /** @param mixed[] $parameters */
    public function render(string $twig, array $parameters): string;
}
