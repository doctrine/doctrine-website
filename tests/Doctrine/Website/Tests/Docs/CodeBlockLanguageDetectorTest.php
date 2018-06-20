<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Doctrine\Website\Docs\CodeBlockLanguageDetector;
use PHPUnit\Framework\TestCase;

class CodeBlockLanguageDetectorTest extends TestCase
{
    /** @var CodeBlockLanguageDetector */
    private $codeBlockLanguageDetector;

    public function testDetectLanguage() : void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('xml', []);

        self::assertEquals('xml', $language);
    }

    public function testDetectLanguageDefault() : void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('', []);

        self::assertEquals('console', $language);
    }

    public function testDetectLanguageAliases() : void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('html+php', []);

        self::assertEquals('php', $language);
    }

    public function testDetectLanguagePhp() : void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('', ['<?php ']);

        self::assertEquals('php', $language);
    }

    public function testDetectLanguageConsole() : void
    {
        $language = $this->codeBlockLanguageDetector->detectLanguage('', ['$ ./doctrine command']);

        self::assertEquals('console', $language);
    }

    protected function setUp() : void
    {
        $this->codeBlockLanguageDetector = new CodeBlockLanguageDetector();
    }
}
