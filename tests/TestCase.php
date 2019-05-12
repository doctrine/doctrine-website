<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests;

use Doctrine\SkeletonMapper\ObjectRepository\ObjectRepositoryInterface;
use Doctrine\Website\Application;
use Doctrine\Website\Model\Contributor;
use Doctrine\Website\Model\Event;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectContributor;
use Doctrine\Website\Repositories\ContributorRepository;
use Doctrine\Website\Repositories\EventRepository;
use Doctrine\Website\Repositories\ProjectContributorRepository;
use Doctrine\Website\Repositories\ProjectRepository;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function rand;

abstract class TestCase extends BaseTestCase
{
    /** @var ContainerBuilder */
    private static $container;

    protected function getContainer() : ContainerBuilder
    {
        if (self::$container === null) {
            self::$container = Application::getContainer('test');
        }

        return self::$container;
    }

    /**
     * @param mixed[] $data
     */
    protected function createModel(string $repositoryClassName, array $data) : object
    {
        /** @var ObjectRepositoryInterface $repository */
        $repository = $this->getContainer()->get($repositoryClassName);

        $object = $repository->create($repository->getClassName());

        $repository->hydrate($object, $data);

        return $object;
    }


    /**
     * @param mixed[] $data
     */
    protected function createEvent(array $data) : Event
    {
        $data['id'] = rand();

        /** @var Event $event */
        $event = $this->createModel(EventRepository::class, $data);

        return $event;
    }


    /**
     * @param mixed[] $data
     */
    protected function createProject(array $data) : Project
    {
        /** @var Project $project */
        $project = $this->createModel(ProjectRepository::class, $data);

        return $project;
    }

    /**
     * @param mixed[] $data
     */
    protected function createContributor(array $data) : Contributor
    {
        /** @var Contributor $contributor */
        $contributor = $this->createModel(ContributorRepository::class, $data);

        return $contributor;
    }

    /**
     * @param mixed[] $data
     */
    protected function createProjectContributor(array $data) : ProjectContributor
    {
        /** @var ProjectContributor $projectContributor */
        $projectContributor = $this->createModel(ProjectContributorRepository::class, $data);

        return $projectContributor;
    }
}
