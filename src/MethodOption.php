<?php

namespace PlainCommands;

use InvalidArgumentException;
use PhpOption\Option;
use PlainCommands\Reflection\MethodDefinition;
use PlainCommands\Reflection\ParameterDefinition;
use PlainCommands\Reflection\Type;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use function Stringy\create as str;

class MethodOption extends CommandOption
{
    /**
     * @var ParameterDefinition
     */
    private $parameterDefinition;

    public function __construct(CommandSet $container, SymfonyCommand $target, MethodDefinition $definition)
    {
        $parameters = $definition->getParameters();
        if (count($parameters) < 1) {
            throw new InvalidArgumentException('A setter method for a command option must have at least one parameter');
        }
        $this->parameterDefinition = $parameters[0];

        parent::__construct($container, $target, $definition);
    }

    public function getName()
    {
        $nameFromDefinition = (string) str($this->definition->getName())->removeLeft('set')->dasherize();

        // From the annotation (first) or from the definition (object property or method)
        return $this->annotation->value ?: $nameFromDefinition;
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

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->parameterDefinition->getType();
    }
}
