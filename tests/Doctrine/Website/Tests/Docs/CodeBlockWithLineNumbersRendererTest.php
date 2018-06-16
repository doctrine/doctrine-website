<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\CodeBlockWithLineNumbersRenderer;
use Highlight\Highlighter;
use PHPUnit\Framework\TestCase;

class CodeBlockWithLineNumbersRendererTest extends TestCase
{
    /** @var Highlighter */
    private $highlighter;

    /** @var CodeBlockWithLineNumbersRenderer */
    private $codeBlockWithLineNumbersRenderer;

    public function testRender() : void
    {
        $rendered = $this->codeBlockWithLineNumbersRenderer->render([
            '<?php',
            '',
            'echo "Hello World";',
        ], 'php');

        $expected = <<<EXPECTED
<pre class="code-block-table"><code class="php"><button
            type="button"
            class="copy-to-clipboard"
            data-copy-element-id="0e59189fbd1fac5920744b486651f171b1a99359"
            title="Copy to Clipboard"
        ><i class="fas fa-copy"></i></button><div id="0e59189fbd1fac5920744b486651f171b1a99359"><table class="code-block-table"><tr><td class="line-number noselect">1</td><td class="code-line" rowspan="3"><span class="hljs-meta">&lt;?php</span>

<span class="hljs-keyword">echo</span> <span class="hljs-string">"Hello World"</span>;</td></tr>
<tr><td class="line-number noselect">2</td></tr>
<tr><td class="line-number noselect">3</td></tr></table></div></code></pre>
EXPECTED;

        self::assertSame($expected, $rendered);
    }

    protected function setUp() : void
    {
        $this->highlighter = new Highlighter();

        $this->codeBlockWithLineNumbersRenderer = new CodeBlockWithLineNumbersRenderer(
            $this->highlighter
        );
    }
}
