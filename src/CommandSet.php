<?php

namespace SimpleCommands;

use function Functional\flat_map;
use function Functional\partial_left;
use SimpleCommands\Reflection\ObjectDefinition;
use SimpleCommands\Annotations;

use function Colada\x;

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

    /**
     * @return Command[]
     */
    public function buildCommands()
    {
        // flat_map() treats Option instances as traversable collections with 1 or 0 elements. So it "opens" them.
        return flat_map(
            $this->object->getClass()->getMethods(),
            partial_left([Command::class, 'create'], $this, $this->buildOptions())
        );
    }

    /**
     * @return PropertyOption[]
     */
    public function buildOptions()
    {
        // flat_map() treats Option instances as traversable collections with 1 or 0 elements. So it "opens" them.
        $properties = flat_map(
            $this->object->getClass()->getProperties(),
            partial_left([PropertyOption::class, 'create'], $this)
        );

        // flat_map() treats Option instances as traversable collections with 1 or 0 elements. So it "opens" them.
        $methods = flat_map(
            $this->object->getClass()->getMethods(),
            partial_left([MethodOption::class, 'create'], $this)
        );

        return array_merge($properties, $methods);
    }
}
