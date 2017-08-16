<?php

namespace SimpleCommands;

use InvalidArgumentException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use SimpleCommands\Reflection\MethodDefinition;
use SimpleCommands\Annotations;

use function Functional\map;
use function Functional\zip;
use function Stringy\create as str;
use function Colada\x;

/**
 * @internal
 */
class Command
{
    /**
     * @var CommandSet
     */
    private $container;

    /**
     * @var MethodDefinition
     */
    private $method;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \PhpOption\Option
     */
    private $annotation;

    /**
     * "Silent" version of constructor (optional return value instead of exception).
     *
     * @param CommandSet $container
     * @param MethodDefinition $method
     *
     * @return Option
     */
    public static function create(CommandSet $container, MethodDefinition $method)
    {
        try {
            $command = new Some(new static($container, $method));
        } catch (InvalidArgumentException $exception) {
            $command = None::create();
        }

        return $command;
    }

    /**
     * @throws InvalidArgumentException If method is not a command.
     *
     * @param CommandSet $container
     * @param MethodDefinition $method
     */
    private function __construct(CommandSet $container, MethodDefinition $method)
    {
        $this->container = $container;
        $this->method = $method;
        $this->annotation = $method->readAnnotation(Annotations\Command::class);

        $this->defineName();
    }

    /**
     * @return bool
     */
    // Doesn't supported at the moment.
//    public function isDefault()
//    {
//        return $this->annotation->map(x()->default)->getOrElse(false);
//    }

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

    public function __invoke(...$arguments)
    {
        return $this->method->invokeFor($this->container->getObject(), $arguments);
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

    // TODO Command options.

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
