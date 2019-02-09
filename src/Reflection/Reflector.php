<?php

namespace SimpleCommands\Reflection;

use phpDocumentor\Reflection\DocBlock;
use PhpOption\Option;
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
     * Doctrine's annotation loader must be registered to working with annotations! See
     * AnnotationRegistry::registerLoader() for details.
     *
     * @param \Doctrine\Common\Annotations\Reader|\Doctrine\Annotations\Reader $annotationReader
     */
    public function __construct($annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param object $object
     *
     * @return ObjectDefinition
     */
    public function reflectObject($object)
    {
        return new ObjectDefinition($object, $this);
    }

    /**
     * @param string|ReflectionClass $class
     *
     * @throws \ReflectionException If the $class is not a valid name
     *
     * @return ClassDefinition
     */
    public function reflectClass($class)
    {
        return new ClassDefinition(
            ($class instanceof ReflectionClass) ? $class : new ReflectionClass($class),
            $this
        );
    }

    /**
     * @param \Reflector $target
     *
     * @return DocBlock
     */
    public function readDocBlock(\Reflector $target)
    {
        return new DocBlock($target);
    }

    /**
     * @param \Reflector $target
     * @param string $type
     *
     * @return Option
     */
    public function readAnnotation(\Reflector $target, $type)
    {
        $annotation = null;
        // TODO To pattern matching.
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
