<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\StaticGenerator\SourceFile;

use Doctrine\Website\StaticGenerator\SourceFile\SourceFileParametersFactory;
use PHPUnit\Framework\TestCase;

class SourceFileParametersFactoryTest extends TestCase
{
    public function testCreateSourceFileParametersOnEmptyContent(): void
    {
        $expected            = [
            'layout' => 'default',
            'title' => '',
        ];
        $sourceFileParameter = (new SourceFileParametersFactory())->createSourceFileParameters('');

        self::assertSame($expected, $sourceFileParameter->getAll());
    }

    public function testCreateSourceFileParameters(): void
    {
        $content             = <<<'TXT'
            ---
            layout: layout1
            title: "How to foo"
            foo: bar
            ---
            Just foo. That's all
            TXT;
        $expected            = [
            'layout' => 'layout1',
            'title' => 'How to foo',
            'foo' => 'bar',
        ];
        $sourceFileParameter = (new SourceFileParametersFactory())->createSourceFileParameters($content);

        self::assertSame($expected, $sourceFileParameter->getAll());
    }
}
