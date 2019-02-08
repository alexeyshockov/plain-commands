<?php

namespace SimpleCommands;

use SimpleCommands\Reflection\ArrayType;
use SimpleCommands\Reflection\ObjectType;
use SimpleCommands\Reflection\ParameterDefinition;

use function Functional\partial_method;
use function PatternMatcher\option_matcher;
use function Colada\x;

class Argument
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
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->parameter->getName();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->parameter->getDescription();
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return !$this->parameter->hasDefaultValue();
    }

    /**
     * @return bool
     */
    public function isArray()
    {
        return $this->parameter->isArrayType();

    }

    /**
     * @return \PhpOption\Option
     */
    public function getClass()
    {
        /*
         * Instead of this:
         *
         * $class = null;
         * if ($this->parameter->getType() instanceof ObjectType) {
         *     $class = $this->parameter->getType()->getClass();
         * }
         *
         * return Option::fromValue($class);
         */
        return option_matcher(function ($type, $object) { return $object instanceof $type; })
            ->addCase(ObjectType::class, x()->getClass())
            ->match($this->parameter->getType())
        ;
    }

    /**
     * @return bool
     */
    public function isInternal()
    {
        return $this->getClass()->isDefined();
    }

    /**
     * @return \PhpOption\Option
     */
    public function getDefaultValue()
    {
        return $this->parameter->getDefaultValue();
    }
}
