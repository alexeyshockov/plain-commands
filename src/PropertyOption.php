<?php

namespace PlainCommands;

use PhpOption\Option;
use PlainCommands\Reflection\Type;

class PropertyOption extends CommandOption
{
    public function getDefaultValue(): Option
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

    public function getType(): Type
    {
        return $this->definition->getType();
    }
}
