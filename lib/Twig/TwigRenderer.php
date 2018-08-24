<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use function sha1;

class TwigRenderer
{
    /** @var MainExtension */
    private $mainExtension;

    /** @var ProjectExtension */
    private $projectExtension;

    /** @var TeamExtension */
    private $teamExtension;

    /** @var BlogExtension */
    private $blogExtension;

    /** @var string */
    private $templatesPath;

    public function __construct(
        MainExtension $mainExtension,
        ProjectExtension $projectExtension,
        TeamExtension $teamExtension,
        BlogExtension $blogExtension,
        string $templatesPath
    ) {
        $this->mainExtension    = $mainExtension;
        $this->projectExtension = $projectExtension;
        $this->teamExtension    = $teamExtension;
        $this->blogExtension    = $blogExtension;
        $this->templatesPath    = $templatesPath;
    }

    /**
     * @param mixed[] $parameters
     */
    public function render(string $twig, array $parameters) : string
    {
        $name = sha1($twig);

        $loader = new ArrayLoader([$name => $twig]);

        $chainLoader = new ChainLoader([
            $loader,
            new FilesystemLoader($this->templatesPath),
        ]);

        $twig = new Environment($chainLoader);
        $twig->addExtension($this->mainExtension);
        $twig->addExtension($this->projectExtension);
        $twig->addExtension($this->teamExtension);
        $twig->addExtension($this->blogExtension);

        return $twig->render($name, $parameters);
    }
}
