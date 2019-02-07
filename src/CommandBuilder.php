<?php

namespace SimpleCommands;

use InvalidArgumentException;
use SimpleCommands\Reflection\Reflector;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use function Functional\flat_map;
use function Functional\partial_left;


class CommandBuilder
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var Reflector
     */
    private $reflector;

    /**
     * @param Application $application
     * @param Reflector $reflector
     */
    public function __construct(Application $application, Reflector $reflector)
    {
        $this->application = $application;
        $this->reflector = $reflector;
    }

    /**
     * @throws InvalidArgumentException For wrong argument (not object).
     *
     * @param object $commandSet
     *
     * @return static
     */
    public function addCommandsFrom($commandSet)
    {
        if (!is_object($commandSet)) {
            throw new InvalidArgumentException('Only objects are supported.');
        }

        foreach ($this->buildCommands($commandSet) as $command) {
            // Add _initialized_ command (aliases must be prepared before this step).
            $this->application->add(
                $command->configure(new SymfonyCommand($command->getFullName()))
            );

            // This isn't supported and should be done by the end user directly.
//            if ($command->isDefault()) {
//                $this->application->setDefaultCommand($command->getFullName());
//            }
        }

        return $this;
    }

    /**
     * @param object $commandSet
     *
     * @return Command[]
     */
    private function buildCommands($commandSet)
    {
        $object = $this->reflector->reflectObject($commandSet);

        $commandSet = new CommandSet($object);

        // flat_map() treats Option instances as traversable collections with 1 or 0 elements. So it "opens" them.
        return flat_map($object->getClass()->getMethods(), partial_left([Command::class, 'create'], $commandSet));
    }
}
