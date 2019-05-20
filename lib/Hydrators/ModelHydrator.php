<?php

declare(strict_types=1);

namespace Doctrine\Website\Hydrators;

use Doctrine\SkeletonMapper\Hydrator\ObjectHydrator;
use Doctrine\SkeletonMapper\Mapping\ClassMetadataInterface;
use Doctrine\SkeletonMapper\ObjectManagerInterface;
use ReflectionProperty;

abstract class ModelHydrator extends ObjectHydrator
{
    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var object */
    private $object;

    /** @var ClassMetadataInterface */
    private $classMetadata;

    /** @var ReflectionProperty[] */
    private $reflectionProperties;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->classMetadata = $this->objectManager->getClassMetadata($this->getClassName());
    }

    /**
     * @param mixed[] $data
     */
    abstract protected function doHydrate(array $data) : void;

    abstract protected function getClassName() : string;

    /**
     * @param object  $object
     * @param mixed[] $data
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function hydrate($object, array $data) : void
    {
        $this->object = $object;

        $this->doHydrate($data);
    }

    /**
     * @return mixed
     */
    public function __get(string $field)
    {
        return $this->getReflectionProperty($field)->getValue($this->object);
    }

    /**
     * @param mixed $value
     */
    public function __set(string $field, $value) : void
    {
        $this->getReflectionProperty($field)->setValue($this->object, $value);
    }

    private function getReflectionProperty(string $field) : ReflectionProperty
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
