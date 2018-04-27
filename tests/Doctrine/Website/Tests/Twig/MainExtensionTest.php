<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Twig\MainExtension;
use PHPUnit\Framework\TestCase;
use function array_keys;
use function current;
use function sort;

class MainExtensionTest extends TestCase
{
    /** @var MainExtension */
    private $mainExtension;

    protected function setUp() : void
    {
        $this->mainExtension = new MainExtension([
            'ocramius' => [
                'active' => true,
                'core' => true,
                'projects' => ['orm'],
            ],
            'jwage' => [
                'active' => true,
                'documentation' => true,
                'projects' => ['orm'],
            ],
            'romanb' => [
                'active' => false,
                'projects' => ['orm'],
            ],
        ]);
    }

    public function testGetAssetUrl() : void
    {
        $url = $this->mainExtension->getAssetUrl(
            '/js/watch.js',
            'http://lcl.doctrine-project.org'
        );

        $this->assertEquals('http://lcl.doctrine-project.org/js/watch.js?cfed72', $url);
    }

    public function testGetTeamMembers() : void
    {
        $teamMembers = $this->mainExtension->getTeamMembers();

        $this->assertEquals([
            'jwage',
            'ocramius',
            'romanb',
        ], array_keys($teamMembers));
    }

    public function testGetActiveCoreTeamMembers() : void
    {
        $teamMembers = $this->mainExtension->getActiveCoreTeamMembers();

        $this->assertEquals(['ocramius'], array_keys($teamMembers));
    }

    public function testGetActiveDocumentationTeamMembers() : void
    {
        $teamMembers = $this->mainExtension->getActiveDocumentationTeamMembers();

        $this->assertEquals(['jwage'], array_keys($teamMembers));
    }

    public function testGetInactiveTeamMembers() : void
    {
        $teamMembers = $this->mainExtension->getInactiveTeamMembers();

        $this->assertEquals(['romanb'], array_keys($teamMembers));
    }

    public function testGetAllProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->mainExtension->getAllProjectTeamMembers($project);

        $this->assertEquals([
            'ocramius',
            'jwage',
            'romanb',
        ], array_keys($teamMembers));
    }

    public function testGetActiveProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->mainExtension->getActiveProjectTeamMembers($project);

        $this->assertEquals([
            'ocramius',
            'jwage',
        ], array_keys($teamMembers));
    }

    public function testGetInactiveProjectTeamMembers() : void
    {
        $project = new Project(['slug' => 'orm']);

        $teamMembers = $this->mainExtension->getInactiveProjectTeamMembers($project);

        $this->assertEquals(['romanb'], array_keys($teamMembers));
    }

    public function testGetDocsUrls() : void
    {
        $urls = $this->mainExtension->getDocsUrls();

        sort($urls);

        $first = current($urls);

        $this->assertEquals('/projects/annotations.html', $first['url']);
    }
}
