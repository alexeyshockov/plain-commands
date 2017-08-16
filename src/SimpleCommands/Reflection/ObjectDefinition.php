<?php

namespace SimpleCommands\Reflection;

class ObjectDefinition extends AbstractDefinition
{
    /**
     * @var object
     */
    private $object;

    /**
     * @param object $object
     * @param Reflector $reflector
     */
    public function __construct($object, Reflector $reflector)
    {
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

    public function getClass()
    {
        return $this->reflector->reflectClass(get_class($this->object));
    }
}
