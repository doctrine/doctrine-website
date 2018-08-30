<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use InvalidArgumentException;
use function assert;
use function file_exists;
use function file_get_contents;
use function is_string;
use function json_decode;
use function sprintf;

class ProjectJsonReader
{
    private const JSON_FILENAME = 'doctrine-project.json';

    /** @var string */
    private $projectsPath;

    public function __construct(string $projectsPath)
    {
        $this->projectsPath = $projectsPath;
    }

    /**
     * @return mixed[]
     */
    public function read(string $repositoryName) : ?array
    {
        $projectJsonPath = $this->projectsPath . '/' . $repositoryName . '/' . self::JSON_FILENAME;

        if (! file_exists($projectJsonPath)) {
            return null;
        }

        $projectJsonString = file_get_contents($projectJsonPath);

        assert(is_string($projectJsonString));

        $projectJson = json_decode($projectJsonString, true);

        if ($projectJson === false || $projectJson === null) {
            throw new InvalidArgumentException(sprintf(
                '%s file exists in repository %s but does not contain any valid data.',
                self::JSON_FILENAME,
                $repositoryName
            ));
        }

        return $projectJson;
    }
}
