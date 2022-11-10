<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_filter;
use function array_map;
use function array_values;
use function end;
use function explode;
use function in_array;
use function is_dir;
use function iterator_to_array;
use function strlen;

class RSTLanguagesDetector
{
    public const ENGLISH_LANGUAGE_CODE = 'en';

    /** @return RSTLanguage[] */
    public function detectLanguages(string $docsDir): array
    {
        if (! is_dir($docsDir)) {
            return [];
        }

        $languages = $this->detectLanguagesInDirectory($docsDir);

        return array_filter($languages, function (RSTLanguage $language): bool {
            return $this->hasRSTIndex($language);
        });
    }

    /** @return RSTLanguage[] */
    private function detectLanguagesInDirectory(string $docsDir): array
    {
        $finder = (new Finder())
            ->directories()
            ->in($docsDir);

        $files = array_values(array_map(static function (SplFileInfo $file): string {
            return (string) $file->getRealPath();
        }, iterator_to_array($finder)));

        $languageCodes = array_map(static function (string $file): string {
            $e = explode('/', $file);

            return end($e);
        }, $files);

        if (in_array(self::ENGLISH_LANGUAGE_CODE, $languageCodes, true)) {
            $languages = [];

            foreach ($languageCodes as $languageCode) {
                if (strlen($languageCode) > 2) {
                    continue;
                }

                $languagePath = $docsDir . '/' . $languageCode;

                $languages[] = new RSTLanguage(
                    $languageCode,
                    $languagePath,
                );
            }

            return $languages;
        }

        return [
            new RSTLanguage(self::ENGLISH_LANGUAGE_CODE, $docsDir),
        ];
    }

    private function hasRSTIndex(RSTLanguage $language): bool
    {
        $finder = (new Finder())
            ->files()
            ->name('index.rst')
            ->in($language->getPath());

        return $finder->count() > 0;
    }
}
