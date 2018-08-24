<?php

declare(strict_types=1);

namespace Doctrine\Website\RST\Directive;

use Doctrine\RST\Directive;
use Doctrine\RST\Nodes\CodeNode;
use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Parser;
use Doctrine\Website\Docs\CodeBlockLanguageDetector;
use Doctrine\Website\Docs\CodeBlockRenderer;
use function array_reverse;
use function preg_split;
use function trim;

/**
 * Renders a code block, example:
 *
 * .. code-block:: php
 *
 *      <?php
 *
 *      echo "Hello world!\n";
 */
class CodeBlockDirective extends Directive
{
    /** @var CodeBlockRenderer */
    private $codeBlockRenderer;

    /** @var CodeBlockLanguageDetector */
    private $codeBlockLanguageDetector;

    public function __construct(
        CodeBlockRenderer $codeBlockRenderer,
        CodeBlockLanguageDetector $codeBlockLanguageDetector
    ) {
        $this->codeBlockRenderer         = $codeBlockRenderer;
        $this->codeBlockLanguageDetector = $codeBlockLanguageDetector;
    }

    public function getName() : string
    {
        return 'code-block';
    }

    /**
     * @param string[] $options
     */
    public function process(
        Parser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ) : void {
        if (! $node instanceof CodeNode) {
            return;
        }

        $kernel = $parser->getKernel();

        $lines = $this->getLines($node->getValue());

        $language = $this->codeBlockLanguageDetector->detectLanguage($data, $lines);

        $node->setLanguage($language);

        $codeBlock = $this->codeBlockRenderer->render($lines, $language);

        $node->setRaw(true);
        $node->setValue($codeBlock);

        if ($variable !== '') {
            $environment = $parser->getEnvironment();
            $environment->setVariable($variable, $node);
        } else {
            $document = $parser->getDocument();
            $document->addNode($node);
        }
    }

    /**
     * @return string[]
     */
    private function getLines(string $code) : array
    {
        $lines = array_reverse(preg_split('/\r\n|\r|\n/', $code));

        // trim empty lines at the end of the code
        foreach ($lines as $key => $line) {
            if (trim($line) !== '') {
                break;
            }

            unset($lines[$key]);
        }

        return array_reverse($lines);
    }

    public function wantCode() : bool
    {
        return true;
    }
}
