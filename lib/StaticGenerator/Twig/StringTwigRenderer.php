<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

use function md5;

class StringTwigRenderer implements TwigRenderer
{
    /** @param AbstractExtension[] $extensions */
    public function __construct(
        private string $templatesDir,
        private array $extensions,
    ) {
    }

    /** @param mixed[] $parameters */
    public function render(string $twig, array $parameters): string
    {
        $name = md5($twig);

        $loader = new ArrayLoader([$name => $twig]);

        $chainLoader = new ChainLoader([
            $loader,
            new FilesystemLoader($this->templatesDir),
        ]);

        $twig = new Environment($chainLoader, ['strict_variables' => true]);

        foreach ($this->extensions as $extension) {
            $twig->addExtension($extension);
        }

        return $twig->render($name, $parameters);
    }
}
