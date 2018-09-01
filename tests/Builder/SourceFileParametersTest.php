<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Builder;

use Doctrine\Website\Builder\SourceFileParameters;
use Doctrine\Website\Tests\TestCase;

class SourceFileParametersTest extends TestCase
{
    /** @var mixed[] */
    private $parameters;

    /** @var SourceFileParameters */
    private $sourceFileParameters;

    public function testGetAll() : void
    {
        self::assertSame($this->parameters, $this->sourceFileParameters->getAll());
    }

    public function testGetParameter() : void
    {
        self::assertCount(2, $this->sourceFileParameters->getAll());
        self::assertTrue($this->sourceFileParameters->getParameter('test1'));
        self::assertEquals('test', $this->sourceFileParameters->getParameter('test2'));
        self::assertNull($this->sourceFileParameters->getParameter('test3'));
    }

    protected function setUp() : void
    {
        $this->parameters = [
            'test1' => true,
            'test2' => 'test',
        ];

        $this->sourceFileParameters = new SourceFileParameters(
            $this->parameters
        );
    }
}
