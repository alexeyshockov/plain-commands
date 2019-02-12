<?php

namespace PlainCommands;

use InvalidArgumentException;
use PhpOption\LazyOption;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use PlainCommands\Reflection\ParameterDefinition;
use PlainCommands\Reflection\ScalarType;
use Stringy\StaticStringy;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Argument implements InputHandler
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
        if (!$definition->getType() instanceof ScalarType) {
            throw new InvalidArgumentException('Only scalar types are supported for command arguments');
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

    public function isRequired(): bool
    {
        return !$this->definition->hasDefaultValue();
    }

    public function getDefaultValue(): Option
    {
        return $this->definition->getDefaultValue();
    }

    private function configure(SymfonyCommand $target)
    {
        $mode = $this->isRequired() ? InputArgument::REQUIRED : InputArgument::OPTIONAL;
        if ($this->definition->getType()->isArray()) {
            $mode |= InputArgument::IS_ARRAY;
        }

        $target->addArgument(
            $this->getName(),
            $mode,
            $this->getDescription(),
            $this->getDefaultValue()->getOrElse(null)
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        return $input->getArgument($this->getName());
    }
}
