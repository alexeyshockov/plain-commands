<?php

namespace SimpleCommands;

use InvalidArgumentException;
use SimpleCommands\Reflection\Reflector;
use Symfony\Component\Console\Application;

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
            throw new InvalidArgumentException('Only objects supported for now.');
        }

        foreach ($this->buildCommands($commandSet) as $command) {
            // TODO Remove the adapter and replace it with '->getSymfonyCommand()' method.
            new CommandAdapter($command, ($sc = new \Symfony\Component\Console\Command\Command($command->getFullName())));

            // Add _initialized_ command (aliases must be prepared before this step).
            $this->application->add($sc);

            // This won't be supported and should be done by the end user directly.
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
