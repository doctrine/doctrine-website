<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Builder;

use DateTimeImmutable;
use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileParameters;
use Doctrine\Website\Builder\SourceFileRenderer;
use Doctrine\Website\Controller\ControllerExecutor;
use Doctrine\Website\Site;
use Doctrine\Website\Twig\TwigRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SourceFileRendererTest extends TestCase
{
    /** @var ControllerExecutor|MockObject */
    private $controllerExecutor;

    /** @var TwigRenderer|MockObject */
    private $twigRenderer;

    /** @var Site|MockObject */
    private $site;

    /** @var SourceFileRenderer */
    private $sourceFileRenderer;

    public function testRenderWithContentTwigBlock() : void
    {
        $date                 = new DateTimeImmutable('2018-09-01');
        $sourceFile           = $this->createMock(SourceFile::class);
        $sourceFileParameters = new SourceFileParameters(['controller' => 'TestController']);
        $controllerResult     = ['test' => true];
        $contents             = 'Test';

        $sourceFile->expects(self::once())
            ->method('getDate')
            ->willReturn($date);

        $sourceFile->expects(self::once())
            ->method('getParameters')
            ->willReturn($sourceFileParameters);

        $sourceFile->expects(self::once())
            ->method('isLayoutNeeded')
            ->willReturn(true);

        $sourceFile->expects(self::once())
            ->method('getParameter')
            ->with('layout')
            ->willReturn('default');

        $this->controllerExecutor->expects(self::once())
            ->method('execute')
            ->with($sourceFile)
            ->willReturn($controllerResult);

        $this->twigRenderer->expects(self::once())
            ->method('render')
            ->with('{% extends "layouts/default.html.twig" %}{% block content %}Test{% endblock %}', [
                'page' => [
                    'date' => $date,
                    'controller' => 'TestController',
                ],
                'site' => $this->site,
                'test' => true,
            ]);

        $this->sourceFileRenderer->render(
            $sourceFile,
            $contents
        );
    }

    public function testRenderWithoutContentTwigBlock() : void
    {
        $date                 = new DateTimeImmutable('2018-09-01');
        $sourceFile           = $this->createMock(SourceFile::class);
        $sourceFileParameters = new SourceFileParameters(['controller' => 'TestController']);
        $controllerResult     = ['test' => true];
        $contents             = '{% block content %}Testing{% endblock %}';

        $sourceFile->expects(self::once())
            ->method('getDate')
            ->willReturn($date);

        $sourceFile->expects(self::once())
            ->method('getParameters')
            ->willReturn($sourceFileParameters);

        $sourceFile->expects(self::once())
            ->method('isLayoutNeeded')
            ->willReturn(true);

        $sourceFile->expects(self::once())
            ->method('getParameter')
            ->with('layout')
            ->willReturn('default');

        $this->controllerExecutor->expects(self::once())
            ->method('execute')
            ->with($sourceFile)
            ->willReturn($controllerResult);

        $this->twigRenderer->expects(self::once())
            ->method('render')
            ->with('{% extends "layouts/default.html.twig" %}{% block content %}Testing{% endblock %}', [
                'page' => [
                    'date' => $date,
                    'controller' => 'TestController',
                ],
                'site' => $this->site,
                'test' => true,
            ]);

        $this->sourceFileRenderer->render(
            $sourceFile,
            $contents
        );
    }

    protected function setUp() : void
    {
        $this->controllerExecutor = $this->createMock(ControllerExecutor::class);
        $this->twigRenderer       = $this->createMock(TwigRenderer::class);
        $this->site               = $this->createMock(Site::class);

        $this->sourceFileRenderer = new SourceFileRenderer(
            $this->controllerExecutor,
            $this->twigRenderer,
            $this->site
        );
    }
}
