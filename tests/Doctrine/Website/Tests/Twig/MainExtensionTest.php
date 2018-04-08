<?php

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Twig\MainExtension;
use PHPUnit\Framework\TestCase;

class MainExtensionTest extends TestCase
{
    /** @var MainExtension */
    private $mainExtension;

    protected function setUp()
    {
        $this->mainExtension = new MainExtension([
            'ocramius' => [],
            'jwage' => [],
        ]);
    }

    public function testGetAssetUrl()
    {
        $url = $this->mainExtension->getAssetUrl(
            '/js/watch.js', 'http://lcl.doctrine-project.org'
        );

        $this->assertEquals('http://lcl.doctrine-project.org/js/watch.js?cfed72', $url);
    }

    public function testGetAllTeamMembers()
    {
        $teamMembers = $this->mainExtension->getAllTeamMembers();

        $this->assertEquals([
            'jwage' => [],
            'ocramius' => [],
        ], $teamMembers);
    }
}
