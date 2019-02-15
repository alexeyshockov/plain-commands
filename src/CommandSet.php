<?php

namespace PlainCommands;

use PlainCommands\Annotations as A;
use PlainCommands\Reflection\ObjectDefinition;
use Stringy\StaticStringy;
use Traversable;
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
        $this->annotation = $object->getClass()->readAnnotation(A\CommandSet::class);
    }

    public function getNamespace(): string
    {
        return $this->annotation->map(function (A\CommandSet $a) {
            return $a->value ?: dasherize($this->definition->getClass()->getName());
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
     * @return Traversable<Command>
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
