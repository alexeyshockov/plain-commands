<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use SimpleCommands\Annotations;
use SimpleCommands\Reflection\MethodDefinition;
use SimpleCommands\Reflection\ParameterDefinition;
use Stringy\StaticStringy;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Colada\x;
use function Functional\map;

class Command
{
    /**
     * @var CommandSet
     */
    private $container;

    /**
     * @var CommandOption[]
     */
    private $globalOptions = [];

    /**
     * @var MethodDefinition
     */
    private $definition;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Option
     */
    private $annotation;

    /**
     * @var SymfonyCommand
     */
    private $target;

    /**
     * Array of handlers, ordered strictly to the method's parameters
     *
     * @var InputHandler[]
     */
    private $parameters = [];

    /**
     * "Silent" version of constructor (optional return value instead of an exception)
     *
     * @param CommandSet       $container
     * @param MethodDefinition $definition
     *
     * @return Option
     */
    public static function create(CommandSet $container, MethodDefinition $definition)
    {
        try {
            return new Some(new static($container, $definition));
        } catch (InvalidArgumentException $exception) {
            return None::create();
        }
    }

    /**
     * @throws InvalidArgumentException If the method is not marked as a command
     *
     * @param CommandSet       $container
     * @param MethodDefinition $definition
     */
    private function __construct(CommandSet $container, MethodDefinition $definition)
    {
        $this->container = $container;
        $this->definition = $definition;
        $this->annotation = $definition->readAnnotation(Annotations\Command::class);

        $this->defineName();

        $this->target = $this->configure(new SymfonyCommand($this->getFullName()));
    }

    /**
     * @param CommandOption[] $commandSetOptions
     *
     * @return $this
     */
    public function setGlobalOptions(array $commandSetOptions)
    {
        $this->globalOptions = $commandSetOptions;

        return $this;
    }

    public function getTarget(): SymfonyCommand
    {
        return $this->target;
    }

    public function isEqualTo(Command $command): bool
    {
        return $this->getFullName() === $command->getFullName();
    }

    private function configure(SymfonyCommand $target): SymfonyCommand
    {
        /*
         * Arguments, options
         */
        $this->buildParameters($target);

        $target
            ->setAliases($this->getShortcuts())
            ->setDescription($this->getDescription())
            // TODO ->addUsage()
            // TODO ->addHelp()
        ;

        return $target->setCode($this);
    }

    /**
     * @see SymfonyCommand::execute()
     * @see SymfonyCommand::setCode()
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function __invoke(InputInterface $input, OutputInterface $output)
    {
        /*
         * Arguments
         */

        $arguments = [];
        /** @var InputHandler $handler */
        foreach ($this->parameters as $handler) {
            $arguments[] = $handler->execute($input, $output);
        }

        /*
         * Global options
         */

        map($this->globalOptions, x()->execute($input, $output));

        // Use return value from the command for the exit code (as in usual Symfony commands).
        return $this->definition->invokeFor($this->container->getObject(), $arguments);
    }

    public function getShortcuts(): array
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
                return $annotation->value ?: (string) StaticStringy::dasherize($this->definition->getName());
            })
            // Let's stick with annotations for now. Skip this feature.
//            ->orElse(Option::fromReturn(function () {
//                if (str($this->method->getName())->endsWith("Command")) {
//                    return (string) str($this->method->getName())->removeRight("Command")->dasherize();
//                }
//            }))
            ->getOrThrow(
                new InvalidArgumentException("Method {$this->definition->getName()}() is not a command.")
            )
        ;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Command name with the namespace (from the command set)
     *
     * @return string
     */
    public function getFullName(): string
    {
        $name = $this->name;
        if ($this->container->getNamespace() !== '') {
            $name = $this->container->getNamespace() . ':' . $name;
        }

        return $name;
    }

    public function getDescription(): string
    {
        return $this->definition->getShortDescription();
    }

    private function buildParameters(SymfonyCommand $target)
    {
        /** @var ParameterDefinition $parameter */
        foreach ($this->definition->getParameters() as $parameter) {
            $runtimeArgument = RuntimeArgument::create($target, $parameter);
            $booleanOption = ParameterOption::create($target, $parameter);
            $argument = Argument::create($target, $parameter);

            $this->parameters[] = $runtimeArgument->orElse($booleanOption)->orElse($argument)->getOrThrow(
                // TODO Add FQCN, like \SimpleCommands\Examples\RepositoryGrabber::loadFromGitHub()
                new InvalidArgumentException("Parameter {$parameter->getName()} cannot be processed")
            );
        }
    }
}
