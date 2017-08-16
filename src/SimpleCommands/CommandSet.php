<?php

namespace SimpleCommands;

use SimpleCommands\Reflection\ObjectDefinition;
use SimpleCommands\Annotations;

use function Colada\x;

/**
 * @internal
 */
class CommandSet
{
    /**
     * @var ObjectDefinition
     */
    private $object;

    /**
     * @var \PhpOption\Option
     */
    private $annotation;

    /**
     * @param ObjectDefinition $object
     */
    public function __construct(ObjectDefinition $object)
    {
        $this->object = $object;
        $this->annotation = $object->getClass()->readAnnotation(Annotations\CommandSet::class);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->annotation->map(x()->value)->getOrElse('');
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object->getObject();
    }
}
