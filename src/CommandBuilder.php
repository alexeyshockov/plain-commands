<?php

namespace SimpleCommands;

use InvalidArgumentException;
use SimpleCommands\Reflection\Reflector;
use Symfony\Component\Console\Application;
use function Functional\flatten;

class CommandBuilder
{
    /**
     * @var Reflector
     */
    private $reflector;

    /**
     * @var CommandSet[]
     */
    private $commandSets = [];

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
        $this->commandSets[] = new CommandSet($this->reflector->reflectObject($object));

        return $this;
    }

    /**
     * @param Application $app
     *
     * @return Application
     */
    public function applyTo(Application $app)
    {
        $commands = [];
        foreach ($this->commandSets as $commandSet) {
            $commands[] = $commandSet->buildCommands();
        }
        $commands = flatten($commands);

        /** @var Command $command */
        foreach ($commands as $command) {
            $app->add($command->getTarget());

            // This isn't supported and should be done by the end user directly.
//            if ($command->isDefault()) {
//                $this->application->setDefaultCommand($command->getFullName());
//            }
        }

        return $app;
    }
}
