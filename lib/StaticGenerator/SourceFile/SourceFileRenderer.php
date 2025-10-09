<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

use Doctrine\Website\StaticGenerator\Controller\ControllerExecutor;
use Doctrine\Website\StaticGenerator\Site;
use Doctrine\Website\StaticGenerator\Twig\TwigRenderer;
use InvalidArgumentException;

use function assert;
use function file_exists;
use function file_get_contents;
use function preg_match_all;
use function sprintf;
use function str_replace;

class SourceFileRenderer
{
    public function __construct(
        private ControllerExecutor $controllerExecutor,
        private TwigRenderer $twigRenderer,
        private Site $site,
        private string $templatesDir,
        private string $sourceDir,
    ) {
    }

    public function render(SourceFile $sourceFile, string $contents): string
    {
        $pageParameters = $this->preparePageParameters($sourceFile);

        $parameters = [
            'page' => $pageParameters,
            'site' => $this->site,
        ];

        if ($sourceFile->hasController()) {
            $controllerResult = $this->controllerExecutor->execute($sourceFile);

            $parameters += $controllerResult->getParameters();

            $controllerTemplate = $controllerResult->getTemplate();

            if ($controllerTemplate !== '') {
                $templatePath = $this->templatesDir . $controllerTemplate;

                if (! file_exists($templatePath)) {
                    throw new InvalidArgumentException(
                        sprintf('Could not find template "%s"', $controllerTemplate),
                    );
                }

                $contents = file_get_contents($templatePath);
            }
        }

        assert($contents !== false);

        $template = $this->prepareTemplate($sourceFile, $contents);

        return $this->twigRenderer->render($template, $parameters);
    }

    /** @return mixed[] */
    private function preparePageParameters(SourceFile $sourceFile): array
    {
        return $sourceFile->getParameters()->getAll() + [
            'date' => $sourceFile->getDate(),
            'sourceFile' => $sourceFile,
            'sourcePath' => $this->getSourceRelativePath($sourceFile),
            'request' => $sourceFile->getRequest(),
        ];
    }

    public function getSourceRelativePath(SourceFile $sourceFile): string
    {
        return str_replace($this->sourceDir, '', $sourceFile->getSourcePath());
    }

    private function prepareTemplate(SourceFile $sourceFile, string $contents): string
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
