<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use SimpleCommands\Annotations;
use SimpleCommands\Reflection\ClassDefinition;
use SimpleCommands\Reflection\MethodDefinition;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Colada\x;
use function Functional\map;
use function Functional\partial_left;
use function Functional\reject;
use function PatternMatcher\option_matcher;
use function Stringy\create as str;

class Command
{
    /**
     * @var CommandSet
     */
    private $container;

    private $options;

    /**
     * @var MethodDefinition
     */
    private $method;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Option
     */
    private $annotation;

    /**
     * "Silent" version of constructor (optional return value instead of an exception).
     *
     * @param CommandSet $container
     * @param array $options
     * @param MethodDefinition $method
     *
     * @return Option
     */
    public static function create(CommandSet $container, $options, MethodDefinition $method)
    {
        try {
            $command = new Some(new static($container, $options, $method));
        } catch (InvalidArgumentException $exception) {
            $command = None::create();
        }

        return $command;
    }

    /**
     * @throws InvalidArgumentException If method is not marked as a command.
     *
     * @param CommandSet $container
     * @param array $options
     * @param MethodDefinition $method
     */
    private function __construct(CommandSet $container, $options, MethodDefinition $method)
    {
        $this->container = $container;
        $this->options = $options;
        $this->method = $method;
        $this->annotation = $method->readAnnotation(Annotations\Command::class);

        $this->defineName();
    }

    /**
     * @param SymfonyCommand $target
     *
     * @return SymfonyCommand
     */
    public function configure(SymfonyCommand $target)
    {
        $target
            ->setAliases($this->getShortcuts())
            ->setDescription($this->getDescription())
            // TODO ->addUsage()
            // TODO ->addHelp()
        ;

        /*
         * Arguments
         */

        /** @var Argument $argument */
        foreach (reject($this->getArguments(), x()->isInternal()) as $argument) {
            $mode = $argument->isRequired() ? InputArgument::REQUIRED : InputArgument::OPTIONAL;
            if ($argument->isArray()) {
                $mode |= InputArgument::IS_ARRAY;
            }

            $target->addArgument(
                $argument->getName(),
                $mode,
                $argument->getDescription(),
                $argument->getDefaultValue()->getOrElse(null)
            );
        }

        /*
         * Options
         */

        // Apply each option to the target command
        map($this->options, x()->configure($target));

        return $target->setCode(partial_left($this, $target));
    }

    /**
     * @see SymfonyCommand::execute()
     * @see SymfonyCommand::setCode()
     *
     * @param SymfonyCommand $target
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function __invoke(SymfonyCommand $target, InputInterface $input, OutputInterface $output)
    {
        /*
         * Arguments
         */

        $matcher = option_matcher(function ($className, ClassDefinition $class) {
            return $class->implementsInterface($className);
        })
            ->addCase(InputInterface::class, $input)
            ->addCase(OutputInterface::class, $output)
            ->addCase(HelperInterface::class, function (ClassDefinition $class) use ($target) {
                foreach ($target->getHelperSet() as $helper) {
                    if ($class->isInterfaceOf($helper)) {
                        return $helper;
                    }
                }

                throw new InvalidArgumentException("Helper with type {$class->getName()} is not registered.");
            })
            // TODO Stopwatch timer?..
        ;

        $arguments = [];
        foreach ($this->getArguments() as $argument) {
            if (!$argument->isInternal()) {
                $arguments[] = $input->getArgument($argument->getName());
            } else {
                $arguments[] = $argument->getClass()
                    ->flatMap($matcher)
                    ->getOrThrow(new InvalidArgumentException(
                        'Parameter $' . $argument->getName() . ': type is missed or not supported.'
                    ));
            }
        }

        /*
         * Options
         */

        map($this->options, x()->execute($input));

        // Use return value from the command for the exit code (as in usual Symfony commands).
        return $this->method->invokeFor($this->container->getObject(), $arguments);
    }

    /**
     * @return array
     */
    public function getShortcuts()
    {
        return $this->annotation->map(x()->shortcuts)->getOrElse([]);
    }

    /**
     * Define name from an annotation, or from a method name.
     *
     * @throws InvalidArgumentException If name can not be extracted.
     */
    private function defineName()
    {
        $this->name = $this->annotation
            ->map(function ($annotation) {
                return $annotation->value ?: (string) str($this->method->getName())->dasherize();
            })
            // Let's stick with annotations for now. Skip this feature.
//            ->orElse(Option::fromReturn(function () {
//                if (str($this->method->getName())->endsWith("Command")) {
//                    return (string) str($this->method->getName())->removeRight("Command")->dasherize();
//                }
//            }))
            ->getOrThrow(
                new InvalidArgumentException("Method {$this->method->getName()}() is not a command.")
            )
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Name with namespace from command set.
     *
     * @return string
     */
    public function getFullName()
    {
        $name = $this->name;
        if ($this->container->getNamespace() !== "") {
            $name = $this->container->getNamespace() . ':' . $name;
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->method->getShortDescription();
    }

    /**
     * @return Argument[]
     */
    public function getArguments()
    {
        return map($this->method->getParameters(), function ($parameter) {
            return new Argument($parameter);
        });
    }
}
