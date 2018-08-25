<?php

declare(strict_types=1);

namespace Doctrine\Website\Builder;

use Doctrine\Website\Site;
use Doctrine\Website\Twig\TwigRenderer;
use function array_merge;
use function preg_match_all;

class SourceFileRenderer
{
    /** @var TwigRenderer */
    private $twigRenderer;

    /** @var Site */
    private $site;

    public function __construct(TwigRenderer $twigRenderer, Site $site)
    {
        $this->twigRenderer = $twigRenderer;
        $this->site         = $site;
    }

    public function render(SourceFile $sourceFile, string $contents) : string
    {
        $template = $this->prepareTemplate($sourceFile, $contents);

        $pageParameters = $this->preparePageParameters($sourceFile);

        return $this->twigRenderer->render($template, [
            'page' => $pageParameters,
            'site' => $this->site,
        ]);
    }

    /**
     * @return mixed[]
     */
    private function preparePageParameters(SourceFile $sourceFile) : array
    {
        return array_merge($sourceFile->getParameters(), [
            'date' => $sourceFile->getDate(),
        ]);
    }

    private function prepareTemplate(SourceFile $sourceFile, string $contents) : string
    {
        if ($sourceFile->isLayoutNeeded()) {
            if (preg_match_all('/{%\s+block\s+(\w+)\s+%}(.*?){%\s+endblock\s+%}/si', $contents, $matches) === 0) {
                $contents = '{% block content %}' . $contents . '{% endblock %}';
            }

            $contents = '{% extends "layouts/' . $sourceFile->getParameter('layout') . '.html.twig" %}' . $contents;
        }

        return $contents;
    }
}
