<?php

namespace SimpleCommands;

use PhpOption\Option;
use SimpleCommands\Reflection\Type;

class PropertyOption extends CommandOption
{
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

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->definition->getType();
    }
}
