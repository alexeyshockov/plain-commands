<?php

namespace SimpleCommands;

use InvalidArgumentException;
use SimpleCommands\Reflection\ClassDefinition;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Colada\x;
use function Functional\reject;
use function PatternMatcher\option_matcher;

/**
 * @internal
 */
class CommandAdapter
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var SymfonyCommand
     */
    private $target;

    /**
     * @param Command $command
     * @param SymfonyCommand $target
     */
    public function __construct(Command $command, SymfonyCommand $target)
    {
        $this->command = $command;
        $this->target = $target;

        $this->configure($target);
    }

    private function configure(SymfonyCommand $target)
    {
        $target
            ->setAliases($this->command->getShortcuts())
            ->setDescription($this->command->getDescription())
            // TODO ->addUsage()
            // TODO ->addHelp()
        ;

        /*
         * Arguments.
         */

        /** @var Argument $argument */
        foreach (reject($this->command->getArguments(), x()->isInternal()) as $argument) {
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
         * Options.
         */



        $target->setCode([$this, 'execute']);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $matcher = option_matcher(function ($className, ClassDefinition $class) {
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

                throw new InvalidArgumentException("Helper with type {$class->getName()} is not registered.");
            })
            // TODO Stopwatch timer?..
            // TODO
        ;

        $arguments = [];
        foreach ($this->command->getArguments() as $argument) {
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

        // Use return value from the command for the exit code (as in usual Symfony commands).
        return call_user_func_array($this->command, $arguments);
    }
}
