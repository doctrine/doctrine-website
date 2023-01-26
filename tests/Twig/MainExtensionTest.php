<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Website\Assets\AssetIntegrityGenerator;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;
use Doctrine\Website\Twig\MainExtension;
use Parsedown;

class MainExtensionTest extends TestCase
{
    private Parsedown $parsedown;

    private AssetIntegrityGenerator $assetIntegrityGenerator;

    private string $sourceDir;

    private string $webpackBuildDir;

    private MainExtension $mainExtension;

    protected function setUp(): void
    {
        $this->parsedown               = $this->createMock(Parsedown::class);
        $this->assetIntegrityGenerator = $this->createMock(AssetIntegrityGenerator::class);
        $this->sourceDir               = __DIR__ . '/../../source';
        $this->webpackBuildDir         = __DIR__ . '/../../.webpack-build';

        $this->mainExtension = new MainExtension(
            $this->parsedown,
            $this->assetIntegrityGenerator,
            $this->sourceDir,
            $this->webpackBuildDir,
            'stripe-publishable-key',
        );
    }

    public function testGetSearchBoxPlaceholder(): void
    {
        $placeholder = $this->mainExtension->getSearchBoxPlaceholder();

        self::assertSame('Search', $placeholder);

        $project = $this->createProject(['shortName' => 'ORM']);

        $placeholder = $this->mainExtension->getSearchBoxPlaceholder($project);

        self::assertSame('Search ORM', $placeholder);

        $project = $this->createProject([
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

    public function testGetAssetUrl(): void
    {
        $url = $this->mainExtension->getAssetUrl(
            '/js/main.js',
            'http://lcl.doctrine-project.org',
        );

        self::assertSame('http://lcl.doctrine-project.org/js/main.js?de1272', $url);
    }
}
