<?php

namespace SimpleCommands\Reflection;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use phpDocumentor\Reflection\DocBlock;
use PhpOption\Option;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class Reflector
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * Doctrine's annotation loader must be registered to working with annotations! See
     * AnnotationRegistry::registerLoader() for details.
     *
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader = null)
    {
        $this->annotationReader = $annotationReader ?: new AnnotationReader();
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
