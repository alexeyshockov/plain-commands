<?php

namespace SimpleCommands\Reflection;

use InvalidArgumentException;

class ObjectDefinition extends AbstractDefinition
{
    /**
     * @var object
     */
    private $object;

    public function __construct($object, Reflector $reflector)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Object is expected');
        }

        parent::__construct($reflector);

        $this->object = $object;
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
        return $this->reflector->reflectClass(get_class($this->object));
    }
}
