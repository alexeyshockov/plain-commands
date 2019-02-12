<?php

namespace SimpleCommands\Reflection;

use InvalidArgumentException;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class Reflector
{
    /**
     * @var \Doctrine\Common\Annotations\Reader|\Doctrine\Annotations\Reader
     */
    private $annotationReader;

    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * Doctrine's annotation loader must be registered to working with annotations! See
     * AnnotationRegistry::registerLoader() for details.
     *
     * @param \Doctrine\Common\Annotations\Reader|\Doctrine\Annotations\Reader $annotationReader
     */
    public function __construct($annotationReader)
    {
        $this->annotationReader = $annotationReader;
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    public function reflectObject($object): ObjectDefinition
    {
        return new ObjectDefinition($object, $this);
    }

    /**
     * @param string|ReflectionClass $class
     *
     * @throws \ReflectionException If the class does not exist
     *
     * @return ClassDefinition
     */
    public function reflectClass($class): ClassDefinition
    {
        return new ClassDefinition(
            ($class instanceof ReflectionClass) ? $class : new ReflectionClass($class),
            $this
        );
    }

    /**
     * @param \Reflector $target
     *
     * @return Option<DocBlock>
     */
    public function readDocBlock(\Reflector $target): Option
    {
        try {
            return new Some($this->docBlockFactory->create($target));
        } catch (InvalidArgumentException $exception) {
            return None::create();
        }
    }

    public function readAnnotation(\Reflector $target, string $type): Option
    {
        $annotation = null;
        if ($target instanceof ReflectionClass) {
            $annotation = $this->annotationReader->getClassAnnotation($target, $type);
        } elseif ($target instanceof ReflectionMethod) {
            $annotation = $this->annotationReader->getMethodAnnotation($target, $type);
        } elseif ($target instanceof ReflectionProperty) {
            $annotation = $this->annotationReader->getPropertyAnnotation($target, $type);
        }

        return Option::fromValue($annotation);
    }
}
