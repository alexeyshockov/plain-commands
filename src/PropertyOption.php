<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use SimpleCommands\Reflection\PropertyDefinition;

class PropertyOption extends CommandOption
{
    /**
     * "Silent" version of constructor (optional return value instead of an exception).
     *
     * @param CommandSet         $container
     * @param PropertyDefinition $definition
     *
     * @return Option
     */
    public static function create(CommandSet $container, PropertyDefinition $definition)
    {
        try {
            $option = new Some(new static($container, $definition));
        } catch (InvalidArgumentException $exception) {
            $option = None::create();
        }

        return $option;
    }

    public function isArray()
    {
        return $this->definition->isArrayType();
    }

    /**
     * @return Option
     */
    public function getDefaultValue()
    {
        return Option::fromValue(
            $this->definition->getValue($this->container->getObject())
        );
    }

    protected function executeForValue($value)
    {
        $this->definition->setValue(
            $this->container->getObject(),
            $value
        );
    }
}
