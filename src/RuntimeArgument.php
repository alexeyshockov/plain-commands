<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\LazyOption;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use SimpleCommands\Reflection\ClassDefinition;
use SimpleCommands\Reflection\ObjectType;
use SimpleCommands\Reflection\ParameterDefinition;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;
use function PatternMatcher\option_matcher;

class RuntimeArgument implements InputHandler
{
    /**
     * @var SymfonyCommand
     */
    private $target;

    /**
     * @var ParameterDefinition
     */
    private $definition;

    /**
     * @var ClassDefinition
     */
    private $class;

    /**
     * "Silent" version of constructor (optional return value instead of an exception)
     *
     * @param SymfonyCommand      $target
     * @param ParameterDefinition $definition
     *
     * @return Option
     */
    public static function create(SymfonyCommand $target, ParameterDefinition $definition)
    {
        return new LazyOption(function () use ($target, $definition) {
            try {
                return new Some(new static($target, $definition));
            } catch (InvalidArgumentException $exception) {
                return None::create();
            }
        });
    }

    public function __construct(SymfonyCommand $target, ParameterDefinition $definition)
    {
        if (!$definition->getType() instanceof ObjectType) {
            throw new InvalidArgumentException('Runtime argument should have a class type hint');
        }

        $this->target = $target;
        $this->definition = $definition;
        $this->class = $definition->getType()->getClass();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        return option_matcher(function ($className, ClassDefinition $class) {
            return $class->implementsInterface($className);
        })
            ->addCase(InputInterface::class, $input)
            ->addCase(OutputInterface::class, $output)
            ->addCase(HelperInterface::class, function (ClassDefinition $class) {
                foreach ($this->target->getHelperSet() as $helper) {
                    if ($class->isInterfaceOf($helper)) {
                        return $helper;
                    }
                }

                throw new UnexpectedValueException("Helper with type {$class->getName()} is not registered.");
            })
            ->match($this->class)
            ->getOrThrow(new UnexpectedValueException(
                // TODO Add FQCN, like \SimpleCommands\Examples\RepositoryGrabber::loadFromGitHub()
                'Parameter $' . $this->definition->getName() . ': class is not supported.'
            ))
        ;
    }
}
