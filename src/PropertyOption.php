<?php

namespace SimpleCommands;


use InvalidArgumentException;
use PhpOption\None;
use PhpOption\Some;
use SimpleCommands\Reflection\ClassDefinition;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Colada\x;
use function Functional\reject;
use function PatternMatcher\option_matcher;
use PhpOption\Option;
use SimpleCommands\Reflection\PropertyDefinition;

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

        $this->annotation = $property->readAnnotation(\SimpleCommands\Annotations\Option::class);
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
        // TODO Default value duplication. Should be removed somehow...
        return $this->annotation->map(x()->valueRequired)->getOrElse(true);
    }

    /**
     * @return array
     */
    public function getShortcuts()
    {
        // TODO Default value duplication. Should be removed somehow...
        return $this->annotation->map(x()->shortcuts)->getOrElse([]);
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

    }

    /**
     * Adapt options from Symfony back to the adaptee object.
     *
     * @param SymfonyCommand $target
     * @param InputInterface $input
     */
    public function __invoke(SymfonyCommand $target, InputInterface $input)
    {
        $value = $input->getOption($this->getName());

        $this->property->setValue(
            $this->container->getObject(),
            $value
        );
    }
}
