<?php

declare(strict_types=1);

namespace Doctrine\Website\Controller;

class ControllerResult
{
    /** @var mixed[] */
    private $result;

    /**
     * @param mixed[] $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed[]
     */
    public function getResult() : array
    {
        return $this->result;
    }
}
