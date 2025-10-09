<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\Request;

interface RequestCollection
{
    /** @return mixed[] */
    public function getRequests(): iterable;
}
