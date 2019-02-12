<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\LazyOption;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use SimpleCommands\Reflection\MethodDefinition;
use SimpleCommands\Reflection\PropertyDefinition;
use SimpleCommands\Reflection\Type;
use Stringy\StaticStringy;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CommandOption implements InputHandler
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

    /**
     * "Silent" version of constructor (optional return value instead of an exception)
     *
     * @param CommandSet                          $container
     * @param SymfonyCommand                      $target
     * @param PropertyDefinition|MethodDefinition $definition
     *
     * @return Option
     */
    public static function create(CommandSet $container, SymfonyCommand $target, $definition)
    {
        return new LazyOption(function () use ($container, $target, $definition) {
            try {
                return new Some(new static($container, $target, $definition));
            } catch (InvalidArgumentException $exception) {
                return None::create();
            }
        });
    }

    public function __construct(CommandSet $container, SymfonyCommand $target, $definition)
    {
        $this->definition = $definition;
        $this->container = $container;

        $this->annotation = $this->definition
            ->readAnnotation(Annotations\Option::class)
            ->getOrThrow(new InvalidArgumentException('The method or property is not a command option'))
        ;

        $this->configure($target);
    }

    public function getName()
    {
        $nameFromDefinition = StaticStringy::dasherize($this->definition->getName());

        // From the annotation (first) or from the definition (object property or method)
        return $this->annotation->value ?: $nameFromDefinition;
    }

    public function getDescription()
    {
        return $this->definition->getShortDescription();
    }

    /**
     * @return string[]
     */
    public function getShortcuts()
    {
        return $this->annotation->shortcuts;
    }

    /**
     * @return Type
     */
    abstract public function getType();

    /**
     * @return Option
     */
    abstract public function getDefaultValue();

    protected function configure(SymfonyCommand $target)
    {
        $mode = $this->getType()->isBoolean() ? InputOption::VALUE_NONE : InputOption::VALUE_REQUIRED;
        if ($this->getType()->isArray()) {
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
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->executeForValue($input->getOption($this->getName()));
    }

    /**
     * @param mixed $value
     */
    abstract protected function executeForValue($value);
}
