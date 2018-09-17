<?php

declare(strict_types=1);

namespace Doctrine\Website\Projects;

use InvalidArgumentException;
use function sprintf;

class ProjectIntegration extends Project
{
    /** @var ProjectIntegrationType */
    private $type;

    /**
     * @param mixed[] $project
     */
    public function __construct(array $project)
    {
        parent::__construct($project);

        if (! isset($project['integrationType'])) {
            throw new InvalidArgumentException(sprintf(
                'Project integration %s requires a type.',
                $this->getName()
            ));
        }

        $this->type = new ProjectIntegrationType($project['integrationType']);
    }

    public function getType() : ProjectIntegrationType
    {
        return $this->type;
    }
}
