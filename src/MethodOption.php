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

    public function getName(): string
    {
        // Annotation value (if set) or build it from the method name
        return $this->annotation->getName()->getOrElse(
            (string) str($this->definition->getName())->removeLeft('set')->dasherize()
        );
    }

    public function getDefaultValue(): Option
    {
        return $this->parameterDefinition->getDefaultValue();
    }

    protected function executeForValue($value)
    {
        if ($value === null && !$this->parameterDefinition->hasDefaultValue()) {
            return;
        }

        $this->definition->invokeFor(
            $this->container->getObject(),
            [$value]
        );
    }

    public function getType(): Type
    {
        return $this->parameterDefinition->getType();
    }
}
