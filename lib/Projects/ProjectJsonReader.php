<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use InvalidArgumentException;
use const JSON_ERROR_NONE;
use function array_replace;
use function assert;
use function file_exists;
use function file_get_contents;
use function is_array;
use function json_decode;
use function json_last_error;
use function sprintf;

class ProjectJsonReader
{
    private const DOCTRINE_PROJECT_JSON_FILE_NAME = '.doctrine-project.json';

    private const COMPOSER_JSON_FILE_NAME = 'composer.json';

    /** @var string */
    private $projectsPath;

    public function __construct(string $projectsPath)
    {
        $this->projectsPath = $projectsPath;
    }

    /**
     * @return mixed[]
     */
    public function read(string $repositoryName) : array
    {
        return array_replace(
            ['repositoryName' => $repositoryName],
            $this->readComposerData($repositoryName),
            $this->readJsonFile($repositoryName, self::DOCTRINE_PROJECT_JSON_FILE_NAME)
        );
    }

    /**
     * @return mixed[]
     */
    private function readComposerData(string $repositoryName) : array
    {
        $data = $this->readJsonFile($repositoryName, self::COMPOSER_JSON_FILE_NAME);

        $composerData = [];

        if (isset($data['name'])) {
            $composerData['composerPackageName'] = $data['name'];
        }

        if (isset($data['description'])) {
            $composerData['description'] = $data['description'];
        }

        if (isset($data['keywords'])) {
            $composerData['keywords'] = $data['keywords'];
        }

        return $composerData;
    }

    /**
     * @return mixed[]
     */
    private function readJsonFile(string $repositoryName, string $fileName) : array
    {
        $filePath = $this->projectsPath . '/' . $repositoryName . '/' . $fileName;

        if (! file_exists($filePath)) {
            return [];
        }

        $jsonString = file_get_contents($filePath);

        assert($jsonString !== false);

        $jsonData = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(sprintf(
                'Failed to parse JSON file in %s',
                $filePath
            ));
        }

        if (! is_array($jsonData)) {
            throw new InvalidArgumentException(sprintf(
                '%s file exists in repository %s but does not contain any valid data.',
                $fileName,
                $repositoryName
            ));
        }

        return $jsonData;
    }
}
