<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Assets;

use Doctrine\Website\Assets\AssetIntegrityGenerator;
use Doctrine\Website\Tests\TestCase;

class AssetIntegrityGeneratorTest extends TestCase
{
    private AssetIntegrityGenerator $assetIntegrityGenerator;

    public function testGetAssetIntegrity(): void
    {
        $integrity = $this->assetIntegrityGenerator->getAssetIntegrity('/css/style.css');

        self::assertSame('sha384-ypIyGShu7WBNc4JDDLBwHLtFojHsqgcjDrbRH9rt5hizlv05qzZgJKBSkJ0X6czC', $integrity);
    }

    public function testGetAssetIntegrityWithRotPath(): void
    {
        $rootDir   = __DIR__ . '/../source';
        $integrity = $this->assetIntegrityGenerator->getAssetIntegrity('/css/style.css', $rootDir);

        self::assertSame('sha384-ypIyGShu7WBNc4JDDLBwHLtFojHsqgcjDrbRH9rt5hizlv05qzZgJKBSkJ0X6czC', $integrity);
    }

    public function testGetWebpackAssetIntegrity(): void
    {
        $integrity = $this->assetIntegrityGenerator->getWebpackAssetIntegrity('/css/style.css');

        self::assertSame('sha384-fxLFv6ZD9hjATuwlBez7aJK5FDk+sXz6nCWy3UdEFbZlrCp1gvZ/lEx1JfRHOCrG', $integrity);
    }

    protected function setUp(): void
    {
        $sourceDir       = __DIR__ . '/../source';
        $webpackBuildDir = __DIR__ . '/../.webpack-build';

        $this->assetIntegrityGenerator = new AssetIntegrityGenerator($sourceDir, $webpackBuildDir);
    }
}
