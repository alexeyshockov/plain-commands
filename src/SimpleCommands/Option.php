<?php

namespace SimpleCommands;

use SimpleCommands\Reflection\ArrayType;
use SimpleCommands\Reflection\ParameterDefinition;

/**
 * @internal
 */
class Option
{
    /**
     * @var ParameterDefinition
     */
    private $parameter;

    /**
     * @param ParameterDefinition $parameter
     */
    public function __construct(ParameterDefinition $parameter)
    {
        $this->parameter = $parameter;

        // TODO Type validation.
    }

    public function getName()
    {
        return $this->parameter->getName();
    }

    public function getDescription()
    {
        return $this->parameter->getDescription();
    }

    public function isRequired()
    {
        return $this->parameter->hasDefaultValue();
    }

    public function isArray()
    {
        return $this->parameter->getType() instanceof ArrayType;
    }

    /**
     * For InputOption::VALUE_NONE.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->parameter->getType()->getName() == "null";
    }

    /**
     * @return \PhpOption\Option
     */
    public function getDefaultValue()
    {
        return $this->parameter->getDefaultValue();
    }
}
