<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\CodeBlockWithLineNumbersRenderer;
use Doctrine\Website\Tests\TestCase;
use Highlight\Highlighter;

class CodeBlockWithLineNumbersRendererTest extends TestCase
{
    private Highlighter $highlighter;

    private CodeBlockWithLineNumbersRenderer $codeBlockWithLineNumbersRenderer;

    public function testRender(): void
    {
        $rendered = $this->codeBlockWithLineNumbersRenderer->render([
            '<?php',
            '',
            'echo "Hello World";',
        ], 'php');

        $expected = <<<'EXPECTED'
<pre class="code-block-table"><code class="php"><button
            type="button"
            class="copy-to-clipboard"
            data-copy-element-id="6c7c6ae7f5433ee1d7f2e10594419ff9e77e1328"
            title="Copy to Clipboard"
        ><i class="fas fa-copy"></i></button><div id="6c7c6ae7f5433ee1d7f2e10594419ff9e77e1328"><table class="code-block-table"><tr><td class="line-number noselect"><a name="line-number-6c7c6ae7f5433ee1d7f2e10594419ff9e77e1328-1" class="line-number-anchor" /><a href="#line-number-6c7c6ae7f5433ee1d7f2e10594419ff9e77e1328-1">1</a></td><td class="code-line" rowspan="3"><span class="hljs-meta">&lt;?php</span>

<span class="hljs-keyword">echo</span> <span class="hljs-string">"Hello World"</span>;</td></tr>
<tr><td class="line-number noselect"><a name="line-number-6c7c6ae7f5433ee1d7f2e10594419ff9e77e1328-2" class="line-number-anchor" /><a href="#line-number-6c7c6ae7f5433ee1d7f2e10594419ff9e77e1328-2">2</a></td></tr>
<tr><td class="line-number noselect"><a name="line-number-6c7c6ae7f5433ee1d7f2e10594419ff9e77e1328-3" class="line-number-anchor" /><a href="#line-number-6c7c6ae7f5433ee1d7f2e10594419ff9e77e1328-3">3</a></td></tr></table></div></code></pre>
EXPECTED;

        self::assertSame($expected, $rendered);
    }

    protected function setUp(): void
    {
        $this->highlighter = new Highlighter();

        $this->codeBlockWithLineNumbersRenderer = new CodeBlockWithLineNumbersRenderer(
            $this->highlighter,
        );
    }
}
