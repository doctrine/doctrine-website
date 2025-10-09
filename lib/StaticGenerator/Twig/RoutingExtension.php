<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Twig;

use Doctrine\Website\StaticGenerator\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoutingExtension extends AbstractExtension
{
    public function __construct(private Router $router)
    {
    }

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', [$this, 'getUrl']),
            new TwigFunction('path', [$this, 'getPath']),
        ];
    }

    /** @param mixed[] $parameters */
    public function getUrl(string $name, array $parameters = [], bool $schemeRelative = false): string
    {
        return $this->router->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /** @param mixed[] $parameters */
    public function getPath(string $name, array $parameters = [], bool $relative = false): string
    {
        return $this->router->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }
}
