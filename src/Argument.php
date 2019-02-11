<?php

namespace SimpleCommands;

use SimpleCommands\Reflection\ObjectType;
use SimpleCommands\Reflection\ParameterDefinition;
use function Colada\x;
use function PatternMatcher\option_matcher;
use Stringy\StaticStringy;

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
        return (string) StaticStringy::dasherize($this->parameter->getName());
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
     * "int", "string", "array" and so on
     *
     * @return bool
     */
    public function isInternal()
    {
        // TODO What about "callable"?
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
