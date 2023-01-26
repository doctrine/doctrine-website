<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs;

use function in_array;

class CodeBlockRenderer
{
    private const CONSOLE_LANGUAGES = ['bash', 'sh', 'console'];

    public function __construct(
        private CodeBlockConsoleRenderer $codeBlockConsoleRenderer,
        private CodeBlockWithLineNumbersRenderer $codeBlockWithLineNumbersRenderer,
    ) {
    }

    /** @param string[] $lines */
    public function render(array $lines, string $language): string
    {
        if (in_array($language, self::CONSOLE_LANGUAGES, true)) {
            return $this->codeBlockConsoleRenderer->render(
                $lines,
            );
        }

        return $this->codeBlockWithLineNumbersRenderer->render(
            $lines,
            $language,
        );
    }
}
