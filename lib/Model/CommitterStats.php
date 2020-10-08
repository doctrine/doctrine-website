<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

interface CommitterStats
{
    public function getNumCommits(): int;

    public function getNumAdditions(): int;

    public function getNumDeletions(): int;
}
