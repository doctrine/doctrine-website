<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\CodeBlockLanguageDetector;
use Doctrine\Website\Tests\TestCase;

class CodeBlockLanguageDetectorTest extends TestCase
{
    /** @var CodeBlockLanguageDetector */
    private $codeBlockLanguageDetector;

    public function testDetectLanguage(): void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('xml', []);

        self::assertSame('xml', $language);
    }

    public function testDetectLanguageDefault(): void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('', []);

        self::assertSame('console', $language);
    }

    public function testDetectLanguageAliases(): void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('html+php', []);

        self::assertSame('php', $language);
    }

    public function testDetectLanguagePhp(): void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('', ['<?php ']);

        self::assertSame('php', $language);
    }

    public function testDetectLanguageConsole(): void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('', ['$ ./bin/console command']);

        self::assertSame('console', $language);
    }

    protected function setUp(): void
    {
        $this->codeBlockLanguageDetector = new CodeBlockLanguageDetector(__DIR__ . '/../..');
    }
}
