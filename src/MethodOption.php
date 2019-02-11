<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use SimpleCommands\Reflection\MethodDefinition;
use SimpleCommands\Reflection\ParameterDefinition;
use function Stringy\create as str;

class MethodOption extends CommandOption
{
    /**
     * @var ParameterDefinition
     */
    private $parameterDefinition;

    /**
     * "Silent" version of constructor (optional return value instead of an exception).
     *
     * @param CommandSet       $container
     * @param MethodDefinition $definition
     *
     * @return Option
     */
    public static function create(CommandSet $container, MethodDefinition $definition)
    {
        try {
            $option = new Some(new static($container, $definition));
        } catch (InvalidArgumentException $exception) {
            $option = None::create();
        }

        return $option;
    }

    public function __construct(CommandSet $container, MethodDefinition $definition)
    {
        parent::__construct($container, $definition);

        $parameters = $this->definition->getParameters();
        if (count($parameters) < 1) {
            throw new \LogicException('A setter method for a command option must have at least one parameter');
        }
        $this->parameterDefinition = $parameters[0];
    }

    public function getName()
    {
        $nameFromDefinition = (string) str($this->definition->getName())->removeLeft('set')->dasherize();

        // From the annotation (first) or from the definition (object property or method)
        return $this->annotation->value ?: $nameFromDefinition;
    }

    public function isArray()
    {
        $parameter = $this->definition->getParameters()[0];

        return $parameter->isArrayType();
    }

    /**
     * @return Option
     */
    public function getDefaultValue()
    {
        return $this->parameterDefinition->getDefaultValue();
    }

    protected function executeForValue($value)
    {
        $this->definition->invokeFor(
            $this->container->getObject(),
            [$value]
        );
    }
}
