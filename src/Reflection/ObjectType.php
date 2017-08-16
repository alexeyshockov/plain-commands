<?php

namespace SimpleCommands\Reflection;

class ObjectType extends Type
{
    /**
     * @var ClassDefinition
     */
    private $class;

    /**
     * @param ClassDefinition $class
     */
    public function __construct(ClassDefinition $class)
    {
        parent::__construct('object');

        $this->class = $class;
    }

    /**
     * @return ClassDefinition
     */
    public function getClass()
    {
        return $this->class;
    }
}
