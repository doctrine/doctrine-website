<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Website\Assets\AssetIntegrityGenerator;
use Doctrine\Website\Docs\CodeBlockLanguageDetector;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;
use Doctrine\Website\Twig\MainExtension;
use Parsedown;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

class MainExtensionTest extends TestCase
{
    private Parsedown $parsedown;

    private AssetIntegrityGenerator $assetIntegrityGenerator;

    private string $sourceDir;

    private string $webpackBuildDir;

    private MainExtension $mainExtension;
    private CodeBlockLanguageDetector&MockObject $codeblockLanguageDetector;

    protected function setUp(): void
    {
        $this->parsedown                 = $this->createMock(Parsedown::class);
        $this->assetIntegrityGenerator   = $this->createMock(AssetIntegrityGenerator::class);
        $this->codeblockLanguageDetector = $this->createMock(CodeBlockLanguageDetector::class);
        $this->sourceDir                 = __DIR__ . '/../../source';
        $this->webpackBuildDir           = __DIR__ . '/../../.webpack-build';

        $this->mainExtension = new MainExtension(
            $this->parsedown,
            $this->assetIntegrityGenerator,
            $this->sourceDir,
            $this->webpackBuildDir,
            $this->codeblockLanguageDetector,
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
            'versions' => new ArrayCollection([
                new ProjectVersion([
                    'slug' => 'latest',
                    'name' => '1.0',
                ]),
            ]),
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

        self::assertMatchesRegularExpression('#^http://lcl.doctrine-project.org/js/main.js\?[a-z0-9+]{6}$#', $url);
    }

    /** @param string[] $lines */
    #[DataProvider('codeDetectionDataProvider')]
    public function testDetectCodeBlockLanguage(string|null $language, string|null $code, array $lines): void
    {
        $this->codeblockLanguageDetector->expects(self::once())
            ->method('detectLanguage')
            ->with($language ?? 'php', $lines)
            ->willReturn('php');

        $language = $this->mainExtension->detectCodeBlockLanguage($language, $code);

        self::assertSame('php', $language);
    }

    /** @return array<array{string|null, string|null, array<string>}> */
    public static function codeDetectionDataProvider(): array
    {
        return [
            [
                'php',
                'code',
                ['code'],
            ],
            [
                null,
                'code',
                ['code'],
            ],
            [
                'sql',
                null,
                [],
            ],
        ];
    }
}
