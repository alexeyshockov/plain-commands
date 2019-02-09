<?php

namespace SimpleCommands;

use InvalidArgumentException;
use SimpleCommands\Reflection\Reflector;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class CommandBuilder
{
    /**
     * @var Reflector
     */
    private $reflector;

    /**
     * @var SymfonyCommand[]
     */
    private $commands;

    public function __construct(Reflector $reflector)
    {
        $this->reflector = $reflector;
    }

    /**
     * @throws InvalidArgumentException For wrong argument (not object).
     *
     * @param object $object
     *
     * @return $this
     */
    public function addCommandsFrom($object)
    {
        $commandSet = new CommandSet($this->reflector->reflectObject($object));

        foreach ($commandSet->buildCommands() as $command) {
            // Add _initialized_ command (aliases must be prepared before this step).
            $this->commands[] = $command->configure(new SymfonyCommand($command->getFullName()));
        }

        return $this;
    }

    /**
     * @param Application $app
     *
     * @return Application
     */
    public function applyTo(Application $app)
    {
        foreach ($this->commands as $command) {
            $app->add($command);

            // This isn't supported and should be done by the end user directly.
//            if ($command->isDefault()) {
//                $this->application->setDefaultCommand($command->getFullName());
//            }
        }

        return $app;
    }
}
