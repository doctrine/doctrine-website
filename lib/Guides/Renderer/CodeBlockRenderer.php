<?php

declare(strict_types=1);

namespace Doctrine\Website\Guides\Renderer;

use Doctrine\Website\Docs\CodeBlockConsoleRenderer;
use Doctrine\Website\Docs\CodeBlockLanguageDetector;
use Doctrine\Website\Docs\CodeBlockWithLineNumbersRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RenderContext;

final class CodeBlockRenderer implements NodeRenderer
{
    private const CONSOLE_LANGUAGES = ['terminal', 'bash', 'sh', 'console'];

    public function __construct(
        private CodeBlockLanguageDetector $codeBlockLanguageDetector,
        private CodeBlockConsoleRenderer $codeBlockConsoleRenderer,
        private CodeBlockWithLineNumbersRenderer $codeBlockWithLineNumbersRenderer,
    ) {
    }

    #[\Override]
    public function supports(string $nodeFqcn): bool
    {
        return $nodeFqcn === CodeNode::class;
    }

    #[\Override]
    public function render(Node $node, RenderContext $renderContext): string
    {
        $lines = explode("\n", $node->getValue());
        $language = $this->codeBlockLanguageDetector->detectLanguage(
            $node->getLanguage(),
            $lines
        );



        if (in_array($language, self::CONSOLE_LANGUAGES, true)) {
            return $this->codeBlockConsoleRenderer->render($lines);
        }

        return $this->codeBlockWithLineNumbersRenderer->render(
            $lines,
            $language,
        );
    }
}
