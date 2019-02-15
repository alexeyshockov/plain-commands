<?php

namespace PlainCommands\Reflection;

use PhpOption\Option;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Traversable;

class ClassDefinition
{
    use StructuralElement;

    public function __construct(ReflectionClass $class, Reflector $reflector)
    {
        $this->reflector = $reflector;
        $this->element = $class;
    }

    /**
     * Only public and not static methods (filter ability may be added later)
     *
     * @return Traversable<MethodDefinition>
     */
    public function getMethods()
    {
        $methods = $this->element->getMethods(ReflectionMethod::IS_PUBLIC & ~ReflectionMethod::IS_STATIC);

        foreach ($methods as $method) {
            yield new MethodDefinition($method, $this->reflector);
        }
    }

    /**
     * All non-static properties (private, protected, public)
     *
     * @return Traversable<PropertyDefinition>
     */
    public function getProperties()
    {
        $properties = $this->element->getProperties(~ReflectionProperty::IS_STATIC);

        foreach ($properties as $property) {
            yield new PropertyDefinition($property, $this->reflector);
        }
    }

    public function implementsInterface(string $interface): bool
    {
        return ($this->element->getName() == $interface) || $this->element->isSubclassOf($interface);
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    public function isInterfaceOf($object)
    {
        return $this->element->isInstance($object);
    }
}
