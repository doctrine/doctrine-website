<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use Doctrine\Common\Inflector\Inflector;
use InvalidArgumentException;
use const JSON_ERROR_NONE;
use function array_replace;
use function assert;
use function file_exists;
use function file_get_contents;
use function is_array;
use function is_dir;
use function json_decode;
use function json_last_error;
use function sprintf;
use function str_replace;

class ProjectDataReader
{
    private const DOCTRINE_PROJECT_JSON_FILE_NAME = '.doctrine-project.json';

    private const COMPOSER_JSON_FILE_NAME = 'composer.json';

    /** @var string */
    private $projectsPath;

    /** @var mixed[] */
    private $projectsData;

    /** @var mixed[] */
    private $projectIntegrationTypes;

    /**
     * @param mixed[] $projectsData
     * @param mixed[] $projectIntegrationTypes
     */
    public function __construct(
        string $projectsPath,
        array $projectsData,
        array $projectIntegrationTypes
    ) {
        $this->projectsPath            = $projectsPath;
        $this->projectsData            = $projectsData;
        $this->projectIntegrationTypes = $projectIntegrationTypes;
    }

    /**
     * @return mixed[]
     */
    public function read(string $repositoryName) : array
    {
        $projectData = array_replace(
            $this->createDefaultProjectData($repositoryName),
            $this->getProjectData($repositoryName),
            $this->readComposerData($repositoryName),
            $this->readJsonFile($repositoryName, self::DOCTRINE_PROJECT_JSON_FILE_NAME)
        );

        if (isset($projectData['integration']) && $projectData['integration'] === true) {
            if (! isset($projectData['integrationType'])) {
                throw new InvalidArgumentException(sprintf(
                    'Project integration %s requires a type.',
                    $projectData['name']
                ));
            }

            if (! isset($this->projectIntegrationTypes[$projectData['integrationType']])) {
                throw new InvalidArgumentException(sprintf(
                    'Project integration %s has a type of %s which does not exist.',
                    $projectData['name'],
                    $projectData['integrationType']
                ));
            }

            $projectData['integrationType'] = $this->projectIntegrationTypes[$projectData['integrationType']];
        }

        if (! isset($projectData['docsSlug'])) {
            $projectData['docsSlug'] = $projectData['slug'];
        }

        return $projectData;
    }

    /**
     * @return mixed[]
     */
    private function createDefaultProjectData(string $repositoryName) : array
    {
        $slug = str_replace('_', '-', Inflector::tableize($repositoryName));

        return [
            'name' => $repositoryName,
            'repositoryName' => $repositoryName,
            'docsPath' => $this->detectDocsPath($repositoryName),
            'codePath' => $this->detectCodePath($repositoryName),
            'slug' => $slug,
            'versions' => [
                [
                    'name' => 'master',
                    'branchName' => 'master',
                    'slug' => 'latest',
                    'aliases' => [
                        'current',
                        'stable',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    private function getProjectData(string $repositoryName) : array
    {
        foreach ($this->projectsData as $projectData) {
            if ($projectData['repositoryName'] === $repositoryName) {
                return $projectData;
            }
        }

        return [];
    }

    private function detectDocsPath(string $repositoryName) : ?string
    {
        return $this->detectPath($repositoryName, ['/docs', '/Resources/doc', '/source'], null);
    }

    private function detectCodePath(string $repositoryName) : ?string
    {
        return $this->detectPath($repositoryName, ['/src', '/lib'], '/');
    }

    /**
     * @param string[] $pathsToCheck
     */
    private function detectPath(string $repositoryName, array $pathsToCheck, ?string $default) : ?string
    {
        foreach ($pathsToCheck as $path) {
            $check = $this->projectsPath . '/' . $repositoryName . $path;

            if (is_dir($check)) {
                return $path;
            }
        }

        return $default;
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
