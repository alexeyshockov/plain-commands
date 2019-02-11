<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\Option;
use SimpleCommands\Reflection\MethodDefinition;
use SimpleCommands\Reflection\PropertyDefinition;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use function Stringy\create as str;

abstract class CommandOption
{
    /**
     * @var CommandSet
     */
    protected $container;

    /**
     * @var PropertyDefinition|MethodDefinition
     */
    protected $definition;

    /**
     * @var \SimpleCommands\Annotations\Option
     */
    protected $annotation;

    protected function __construct(CommandSet $container, $definition)
    {
        $this->definition = $definition;
        $this->container = $container;

        $this->annotation = $this->definition
            ->readAnnotation(Annotations\Option::class)
            ->getOrThrow(new InvalidArgumentException('The method is not a command option'))
        ;
    }

    public function getName()
    {
        $nameFromDefinition = (string) str($this->definition->getName())->dasherize();

        // From the annotation (first) or from the definition (object property or method)
        return $this->annotation->value ?: $nameFromDefinition;
    }

    public function getDescription()
    {
        return $this->definition->getShortDescription();
    }

    public function isValueRequired()
    {
        return $this->annotation->valueRequired;
    }

    /**
     * @return string[]
     */
    public function getShortcuts()
    {
        return $this->annotation->shortcuts;
    }

    abstract public function isArray();

    /**
     * @return Option
     */
    abstract public function getDefaultValue();

    public function configure(SymfonyCommand $target)
    {
        // TODO Boolean options as InputOption::VALUE_NONE

        $mode = $this->isValueRequired() ? InputOption::VALUE_REQUIRED : InputOption::VALUE_OPTIONAL;
        if ($this->isArray()) {
            $mode |= InputOption::VALUE_IS_ARRAY;
        }

        $target->addOption(
            $this->getName(),
            $this->getShortcuts(),
            $mode,
            $this->getDescription(),
            $this->getDefaultValue()->getOrElse(null)
        );
    }

    abstract protected function executeForValue($value);

    /**
     * Adapt options from Symfony back to the adaptee object.
     *
     * @param InputInterface $input
     */
    public function execute(InputInterface $input)
    {
        $this->executeForValue($input->getOption($this->getName()));
    }
}
