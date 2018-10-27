<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function array_map;
use function array_values;
use function end;
use function explode;
use function in_array;
use function iterator_to_array;
use function strlen;

class RSTLanguagesDetector
{
    public const ENGLISH_LANGUAGE_CODE = 'en';

    /** @var string */
    private $projectsDir;

    public function __construct(string $projectsDir)
    {
        $this->projectsDir = $projectsDir;
    }

    /**
     * @return RSTLanguage[]
     */
    public function detectLanguages(Project $project, ProjectVersion $version) : array
    {
        $finder = new Finder();

        $docsDir = $project->getAbsoluteDocsPath($this->projectsDir);

        $finder
            ->directories()
            ->in($docsDir);

        $files = array_values(array_map(static function (SplFileInfo $file) {
            return $file->getRealPath();
        }, iterator_to_array($finder)));

        $languageCodes = array_map(static function (string $file) {
            $e = explode('/', $file);

            return end($e);
        }, $files);

        if (in_array(self::ENGLISH_LANGUAGE_CODE, $languageCodes, true)) {
            $languages = [];

            foreach ($languageCodes as $languageCode) {
                if (strlen($languageCode) > 2) {
                    continue;
                }

                $languagePath = $project->getAbsoluteDocsPath($this->projectsDir) . '/' . $languageCode;

                $languages[] = new RSTLanguage(
                    $languageCode,
                    $languagePath
                );
            }

            return $languages;
        }

        return [
            new RSTLanguage(self::ENGLISH_LANGUAGE_CODE, $project->getAbsoluteDocsPath($this->projectsDir)),
        ];
    }
}
