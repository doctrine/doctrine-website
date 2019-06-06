<?php

declare(strict_types=1);

namespace Doctrine\Website\Twitter;

interface CreateTweet
{
    public function __invoke(string $message) : bool;
}
