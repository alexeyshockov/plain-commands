<?php

namespace SimpleCommands\Reflection;

use PhpOption\Option;
use ReflectionClass;
use ReflectionMethod;

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
     * @return array
     */
    public function getMethods()
    {
        $methods = $this->class->getMethods(ReflectionMethod::IS_PUBLIC);

        $definitions = [];
        foreach ($methods as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $definitions[] = new MethodDefinition($method, $this->reflector);
        }

        return $definitions;
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
