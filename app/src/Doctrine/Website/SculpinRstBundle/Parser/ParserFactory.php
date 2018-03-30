<?php

namespace Doctrine\Website\SculpinRstBundle\Parser;

use Gregwar\RST\Kernel;
use Gregwar\RST\Parser;

class ParserFactory
{
    /**
     * @var Kernel
     */
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function createParser()
    {
        return new Parser(null, $this->kernel);
    }
}
