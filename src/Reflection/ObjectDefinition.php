<?php

namespace PlainCommands\Reflection;

use InvalidArgumentException;

class ObjectDefinition
{
    /**
     * @var Reflector
     */
    private $reflector;

    /**
     * @var object
     */
    private $object;

    public function __construct($object, Reflector $reflector)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Object is expected');
        }

        $this->object = $object;
        $this->reflector = $reflector;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    public function getClass(): ClassDefinition
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->reflector->reflectClass(get_class($this->object));
    }
}
