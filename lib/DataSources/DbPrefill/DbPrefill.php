<?php

declare(strict_types=1);

namespace Doctrine\Website\DataSources\DbPrefill;

interface DbPrefill
{
    public function populate(): void;
}
