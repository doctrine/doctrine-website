<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Assets\AssetIntegrityGenerator;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;
use Doctrine\Website\Twig\MainExtension;
use Parsedown;

class MainExtensionTest extends TestCase
{
    /** @var Parsedown */
    private $parsedown;

    /** @var AssetIntegrityGenerator */
    private $assetIntegrityGenerator;

    /** @var string */
    private $sourceDir;

    /** @var MainExtension */
    private $mainExtension;

    protected function setUp() : void
    {
        $this->parsedown               = $this->createMock(Parsedown::class);
        $this->assetIntegrityGenerator = $this->createMock(AssetIntegrityGenerator::class);
        $this->sourceDir               = __DIR__ . '/../../source';

        $this->mainExtension = new MainExtension(
            $this->parsedown,
            $this->assetIntegrityGenerator,
            $this->sourceDir
        );
    }

    public function testGetSearchBoxPlaceholder() : void
    {
        $placeholder = $this->mainExtension->getSearchBoxPlaceholder();

        self::assertSame('Search', $placeholder);

        $project = new Project(['shortName' => 'ORM']);

        $placeholder = $this->mainExtension->getSearchBoxPlaceholder($project);

        self::assertSame('Search ORM', $placeholder);

        $project = new Project([
            'shortName' => 'ORM',
            'versions' => [
                [
                    'slug' => 'latest',
                    'name' => '1.0',
                ],
            ],
        ]);

        $projectVersion = new ProjectVersion(['name' => '1.0']);

        $placeholder = $this->mainExtension->getSearchBoxPlaceholder($project, $projectVersion);

        self::assertSame('Search ORM 1.0', $placeholder);
    }

    public function testGetAssetUrl() : void
    {
        $url = $this->mainExtension->getAssetUrl(
            '/js/main.js',
            'http://lcl.doctrine-project.org'
        );

        self::assertSame('http://lcl.doctrine-project.org/js/main.js?4138a7', $url);
    }
}
