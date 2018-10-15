<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Assets;

use Doctrine\Website\Assets\AssetIntegrityGenerator;
use PHPUnit\Framework\TestCase;

class AssetIntegrityGeneratorTest extends TestCase
{
    /** @var string */
    private $sourceDir;

    /** @var AssetIntegrityGenerator */
    private $assetIntegrityGenerator;

    public function testGetAssetIntegrity() : void
    {
        $integrity = $this->assetIntegrityGenerator->getAssetIntegrity('/css/style.css');

        self::assertSame('sha384-ypIyGShu7WBNc4JDDLBwHLtFojHsqgcjDrbRH9rt5hizlv05qzZgJKBSkJ0X6czC', $integrity);
    }

    protected function setUp() : void
    {
        $this->sourceDir = __DIR__ . '/../source';

        $this->assetIntegrityGenerator = new AssetIntegrityGenerator($this->sourceDir);
    }
}
