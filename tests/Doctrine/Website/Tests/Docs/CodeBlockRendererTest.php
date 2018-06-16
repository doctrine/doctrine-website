<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\CodeBlockConsoleRenderer;
use Doctrine\Website\Docs\CodeBlockRenderer;
use Doctrine\Website\Docs\CodeBlockWithLineNumbersRenderer;
use PHPUnit\Framework\TestCase;

class CodeBlockRendererTest extends TestCase
{
    /** @var CodeBlockConsoleRenderer */
    private $codeBlockConsoleRenderer;

    /** @var CodeBlockWithLineNumbersRenderer */
    private $codeBlockWithLineNumbersRenderer;

    /** @var CodeBlockRenderer */
    private $codeBlockRenderer;

    public function testRenderCodeBlockWithLineNumbers() : void
    {
        $lines = [
            '<?php',
            '',
            'echo "Hello World";',
        ];

        $language = 'php';

        $this->codeBlockWithLineNumbersRenderer->expects($this->once())
            ->method('render')
            ->with($lines, $language)
            ->willReturn('expected');

        self::assertEquals('expected', $this->codeBlockRenderer->render($lines, $language));
    }

    /**
     * @dataProvider getConsoleLanguages
     *
     */
    public function testRenderConsole(string $consoleLanguage) : void
    {
        $lines = [
            '<?php',
            '',
            'echo "Hello World";',
        ];

        $this->codeBlockConsoleRenderer->expects($this->once())
            ->method('render')
            ->with($lines)
            ->willReturn('expected');

        self::assertEquals('expected', $this->codeBlockRenderer->render($lines, $consoleLanguage));
    }

    /**
     * @return string[][]
     */
    public function getConsoleLanguages() : array
    {
        return [
            ['console'],
            ['bash'],
            ['sh'],
        ];
    }

    protected function setUp() : void
    {
        $this->codeBlockConsoleRenderer         = $this->createMock(CodeBlockConsoleRenderer::class);
        $this->codeBlockWithLineNumbersRenderer = $this->createMock(CodeBlockWithLineNumbersRenderer::class);

        $this->codeBlockRenderer = new CodeBlockRenderer(
            $this->codeBlockConsoleRenderer,
            $this->codeBlockWithLineNumbersRenderer
        );
    }
}
