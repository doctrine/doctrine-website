<?php

declare(strict_types=1);

namespace Doctrine\Website\Builder;

use Doctrine\Website\Controller\ControllerExecutor;
use Doctrine\Website\Site;
use Doctrine\Website\Twig\TwigRenderer;
use function preg_match_all;
use function str_replace;

class SourceFileRenderer
{
    /** @var ControllerExecutor */
    private $controllerExecutor;

    /** @var TwigRenderer */
    private $twigRenderer;

    /** @var Site */
    private $site;

    /** @var string */
    private $sourcePath;

    public function __construct(
        ControllerExecutor $controllerExecutor,
        TwigRenderer $twigRenderer,
        Site $site,
        string $sourcePath
    ) {
        $this->controllerExecutor = $controllerExecutor;
        $this->twigRenderer       = $twigRenderer;
        $this->site               = $site;
        $this->sourcePath         = $sourcePath;
    }

    public function render(SourceFile $sourceFile, string $contents) : string
    {
        $template = $this->prepareTemplate($sourceFile, $contents);

        $pageParameters = $this->preparePageParameters($sourceFile);

        $parameters = [
            'page' => $pageParameters,
            'site' => $this->site,
        ];

        if (isset($parameters['page']['controller'])) {
            $controllerParameters = $this->controllerExecutor->execute($sourceFile);

            $parameters += $controllerParameters;
        }

        return $this->twigRenderer->render($template, $parameters);
    }

    /**
     * @return mixed[]
     */
    private function preparePageParameters(SourceFile $sourceFile) : array
    {
        return $sourceFile->getParameters()->getAll() + [
            'date' => $sourceFile->getDate(),
            'sourceFile' => str_replace($this->sourcePath, '/source', $sourceFile->getSourcePath()),
        ];
    }

    private function prepareTemplate(SourceFile $sourceFile, string $contents) : string
    {
        if ($sourceFile->isLayoutNeeded()) {
            if ($contents !== '') {
                $regex = '/{%\s+block\s+(\w+)\s+%}(.*?){%\s+endblock\s+%}/si';

                if (preg_match_all($regex, $contents, $matches) === 0) {
                    $contents = '{% block content %}' . $contents . '{% endblock %}';
                }
            }

            $contents = '{% extends "layouts/' . $sourceFile->getParameter('layout') . '.html.twig" %}' . $contents;
        }

        return $contents;
    }
}
