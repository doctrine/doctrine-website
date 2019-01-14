<?php

declare(strict_types=1);

namespace Doctrine\Website\Controllers;

use Doctrine\StaticWebsiteGenerator\Controller\Response;

class SponsorshipController
{
    public function index() : Response
    {
        return new Response([]);
    }
}
