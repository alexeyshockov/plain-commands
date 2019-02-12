<?php

namespace SimpleCommands\Reflection;

class ObjectType extends Type
{
    /**
     * @var ClassDefinition
     */
    private $class;

    public function __construct(ClassDefinition $class)
    {
        // TODO FQCN instead of 'object'?..
        parent::__construct('object');

        $this->class = $class;
    }

    public function getClass(): ClassDefinition
    {
        return $this->class;
    }
}
