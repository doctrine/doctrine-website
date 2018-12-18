<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

interface DataBuilder
{
    public function build() : WebsiteData;
}
