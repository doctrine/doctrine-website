<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources;

interface DataSource
{
    /** @return mixed[][] */
    public function getSourceRows(): array;
}
