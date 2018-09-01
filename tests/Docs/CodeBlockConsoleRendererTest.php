<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\CodeBlockConsoleRenderer;
use Doctrine\Website\Tests\TestCase;

class CodeBlockConsoleRendererTest extends TestCase
{
    /** @var CodeBlockConsoleRenderer */
    private $codeBlockConsoleRenderer;

    public function testRenderWithDollarSign() : void
    {
        $rendered = $this->codeBlockConsoleRenderer->render([
            '$      ./bin/console command',
            'output',
            'more output',
        ]);

        $expected = <<<EXPECTED
<div class="console"><pre><code class="console"><span class="noselect">$ </span>./bin/console command
output
more output</code></pre></div>
EXPECTED;

        self::assertSame($expected, $rendered);
    }

    public function testRenderWithoutDollarSign() : void
    {
        $rendered = $this->codeBlockConsoleRenderer->render(['      ./bin/console command']);

        $expected = <<<EXPECTED
<div class="console"><pre><code class="console"><span class="noselect">$ </span>./bin/console command
</code></pre></div>
EXPECTED;

        self::assertSame($expected, $rendered);
    }

    protected function setUp() : void
    {
        $this->codeBlockConsoleRenderer = new CodeBlockConsoleRenderer();
    }
}
