<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\CodeBlockConsoleRenderer;
use PHPUnit\Framework\TestCase;

class CodeBlockConsoleRendererTest extends TestCase
{
    /** @var CodeBlockConsoleRenderer */
    private $codeBlockConsoleRenderer;

    public function testRenderWithDollarSign() : void
    {
        $rendered = $this->codeBlockConsoleRenderer->render([
            '$      ./doctrine command',
            'output',
            'more output',
        ]);

        $expected = <<<EXPECTED
<div class="console"><pre><code class="console"><span class="noselect">$ </span>./doctrine command
output
more output</code></pre></div>
EXPECTED;

        self::assertSame($expected, $rendered);
    }

    public function testRenderWithoutDollarSign() : void
    {
        $rendered = $this->codeBlockConsoleRenderer->render(['      ./doctrine command']);

        $expected = <<<EXPECTED
<div class="console"><pre><code class="console"><span class="noselect">$ </span>./doctrine command
</code></pre></div>
EXPECTED;

        self::assertSame($expected, $rendered);
    }

    protected function setUp() : void
    {
        $this->codeBlockConsoleRenderer = new CodeBlockConsoleRenderer();
    }
}
