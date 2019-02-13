<?php

namespace PlainCommands;

use InvalidArgumentException;
use PhpOption\LazyOption;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use PlainCommands\Reflection\ParameterDefinition;
use Stringy\StaticStringy;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Represents a boolen method's parameter (this is the only case when a parameter is mapped to a command option)
 */
class ParameterOption implements InputHandler
{
    /**
     * @var ParameterDefinition
     */
    private $definition;

    /**
     * "Silent" version of constructor (optional return value instead of an exception)
     *
     * @param SymfonyCommand      $target
     * @param ParameterDefinition $definition
     *
     * @return Option<self>
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
        if (!$definition->getType()->isBoolean()) {
            throw new InvalidArgumentException('Only boolean');
        }

        $this->definition = $definition;

        $this->configure($target);
    }

    public function getName(): string
    {
        return (string) StaticStringy::dasherize($this->definition->getName());
    }

    public function getDescription(): string
    {
        return $this->definition->getDescription();
    }

    private function configure(SymfonyCommand $target)
    {
        $target->addOption(
            $this->getName(),
            null,
            InputOption::VALUE_NONE,
            $this->getDescription()
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        return $input->getOption($this->getName());
    }
}
