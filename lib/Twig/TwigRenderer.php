<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{
    /** @var MainExtension */
    private $mainExtension;

    /** @var ProjectExtension */
    private $projectExtension;

    /** @var string */
    private $templatesPath;

    public function __construct(
        MainExtension $mainExtension,
        ProjectExtension $projectExtension,
        string $templatesPath
    ) {
        $this->mainExtension    = $mainExtension;
        $this->projectExtension = $projectExtension;
        $this->templatesPath    = $templatesPath;
    }

    /**
     * @param mixed[] $parameters
     */
    public function render(string $twig, array $parameters) : string
    {
        $name = $parameters['page']['url'];

        $loader = new ArrayLoader([$name => $twig]);

        $chainLoader = new ChainLoader([
            $loader,
            new FilesystemLoader($this->templatesPath),
        ]);

        $twig = new Environment($chainLoader, ['strict_variables' => true]);
        $twig->addExtension($this->mainExtension);
        $twig->addExtension($this->projectExtension);

        return $twig->render($name, $parameters);
    }
}
