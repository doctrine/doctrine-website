<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use Doctrine\SkeletonMapper\Hydrator\ObjectHydrator;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;
use ReflectionProperty;

abstract class ModelHydrator extends ObjectHydrator
{
    private object $object;

    private ClassMetadataInterface $classMetadata;

    /** @var ReflectionProperty[] */
    private array $reflectionProperties;

    public function __construct(protected ObjectManagerInterface $objectManager)
    {
        $this->classMetadata = $this->objectManager->getClassMetadata($this->getClassName());
    }

    /** @param mixed[] $data */
    abstract protected function doHydrate(array $data): void;

    abstract protected function getClassName(): string;

    /**
     * @param mixed[] $data
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function hydrate(object $object, array $data): void
    {
        $this->object = $object;

        $this->doHydrate($data);
    }

    public function __get(string $field): mixed
    {
        return $this->getReflectionProperty($field)->getValue($this->object);
    }

    public function __set(string $field, mixed $value): void
    {
        $this->getReflectionProperty($field)->setValue($this->object, $value);
    }

    private function getReflectionProperty(string $field): ReflectionProperty
    {
        $key = $this->getClassName() . '::' . $field;

        if (! isset($this->reflectionProperties[$key])) {
            $reflectionProperty = $this->classMetadata->getReflectionClass()->getProperty($field);
            $reflectionProperty->setAccessible(true);

            $this->reflectionProperties[$key] = $reflectionProperty;
        }

        return $this->reflectionProperties[$key];
    }
}
