<?php

namespace PlainCommands\Reflection;

use PhpOption\Option;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Traversable;

class ClassDefinition extends AbstractDefinition
{
    /**
     * @var ReflectionClass
     */
    private $class;

    /**
     * @param ReflectionClass $class
     * @param Reflector $reflector
     */
    public function __construct(ReflectionClass $class, Reflector $reflector)
    {
        parent::__construct($reflector);

        $this->class = $class;
    }

    public function getName()
    {
        return $this->class->getName();
    }

    /**
     * @param string $type
     *
     * @return Option
     */
    public function readAnnotation($type)
    {
        return $this->reflector->readAnnotation($this->class, $type);
    }

    /**
     * Only public and not static methods (filter ability may be added later).
     *
     * @return iterable<MethodDefinition>
     */
    public function getMethods()
    {
        $methods = $this->class->getMethods(ReflectionMethod::IS_PUBLIC & ~ReflectionMethod::IS_STATIC);

        foreach ($methods as $method) {
            yield new MethodDefinition($method, $this->reflector);
        }
    }

    /**
     * All non-static properties (private, protected, public)
     *
     * @return iterable<PropertyDefinition>
     */
    public function getProperties()
    {
        $properties = $this->class->getProperties(~ReflectionProperty::IS_STATIC);

        foreach ($properties as $property) {
            yield new PropertyDefinition($property, $this->reflector);
        }
    }

    /**
     * @param string $interface Interface (it can be a class, an interface or even a trait) name.
     *
     * @return bool
     */
    public function implementsInterface($interface)
    {
        return ($this->class->getName() == $interface) || $this->class->isSubclassOf($interface);
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    public function isInterfaceOf($object)
    {
        return $this->class->isInstance($object);
    }
}
