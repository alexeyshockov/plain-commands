<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use SimpleCommands\Reflection\PropertyDefinition;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use function Colada\x;

class PropertyOption
{
    /**
     * @var CommandSet
     */
    private $container;

    /**
     * @var PropertyDefinition
     */
    private $property;

    /**
     * @var Option
     */
    private $annotation;

    /**
     * "Silent" version of constructor (optional return value instead of an exception).
     *
     * @param CommandSet         $container
     * @param PropertyDefinition $property
     *
     * @return Option
     */
    public static function create(CommandSet $container, PropertyDefinition $property)
    {
        try {
            $option = new Some(new static($container, $property));
        } catch (InvalidArgumentException $exception) {
            $option = None::create();
        }

        return $option;
    }

    public function __construct(CommandSet $container, PropertyDefinition $property)
    {
        $this->property = $property;
        $this->container = $container;

        $this->annotation = $property
            ->readAnnotation(Annotations\Option::class)
            ->getOrThrow(new InvalidArgumentException('The property is not a command option'))
        ;
    }

    public function getName()
    {
        return $this->property->getName();
    }

    public function getDescription()
    {
        return $this->property->getShortDescription();
    }

    public function isValueRequired()
    {
        return $this->annotation->valueRequired;
    }

    /**
     * @return array
     */
    public function getShortcuts()
    {
        return $this->annotation->shortcuts;
    }

    public function isArray()
    {
        return $this->property->isArrayType();
    }

    /**
     * @return Option
     */
    public function getDefaultValue()
    {
        return Option::fromValue(
            $this->property->getValue($this->container->getObject())
        );
    }

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

    /**
     * Adapt options from Symfony back to the adaptee object.
     *
     * @param InputInterface $input
     */
    public function execute(InputInterface $input)
    {
        $value = $input->getOption($this->getName());

        $this->property->setValue(
            $this->container->getObject(),
            $value
        );
    }
}
