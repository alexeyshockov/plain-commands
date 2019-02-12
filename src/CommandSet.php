<?php

namespace SimpleCommands;

use function Functional\map;
use SimpleCommands\Annotations;
use SimpleCommands\Reflection\ObjectDefinition;
use Stringy\StaticStringy;
use function Functional\flat_map;
use function Functional\partial_left;

class CommandSet
{
    /**
     * @var ObjectDefinition
     */
    private $definition;

    /**
     * @var \PhpOption\Option
     */
    private $annotation;

    /**
     * @param ObjectDefinition $object
     */
    public function __construct(ObjectDefinition $object)
    {
        $this->definition = $object;
        $this->annotation = $object->getClass()->readAnnotation(Annotations\CommandSet::class);
    }

    public function getNamespace(): string
    {
        return $this->annotation->map(function (Annotations\CommandSet $annotation) {
            return $annotation->value ?: (string) StaticStringy::dasherize($this->definition->getClass()->getName());
        })->getOrElse('');
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->definition->getObject();
    }

    /**
     * @return iterable Command[] generator
     */
    public function buildCommands()
    {
        // flat_map() treats Option instances as traversable collections with 1 or 0 elements. So it "opens" them.
        $commands = flat_map(
            $this->definition->getClass()->getMethods(),
            partial_left([Command::class, 'create'], $this)
        );

        /** @var Command $command */
        foreach ($commands as $command) {
            yield $command->setGlobalOptions($this->buildOptionsFor($command));
        }
    }

    /**
     * @param Command $command
     *
     * @return PropertyOption[]
     */
    private function buildOptionsFor(Command $command)
    {
        // flat_map() treats Option instances as traversable collections with 1 or 0 elements. So it "opens" them.
        $properties = flat_map(
            $this->definition->getClass()->getProperties(),
            partial_left([PropertyOption::class, 'create'], $this, $command->getTarget())
        );

        // flat_map() treats Option instances as traversable collections with 1 or 0 elements. So it "opens" them.
        $methods = flat_map(
            $this->definition->getClass()->getMethods(),
            partial_left([MethodOption::class, 'create'], $this, $command->getTarget())
        );

        return array_merge($properties, $methods);
    }
}
