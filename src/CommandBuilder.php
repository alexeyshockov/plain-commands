<?php

namespace PlainCommands;

use InvalidArgumentException;
use PlainCommands\Reflection\Reflector;
use Symfony\Component\Console\Application;

use function Colada\x;
use function Functional\flatten;
use function Functional\map;

/**
 * @api
 */
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
        /*
         * Instead of:
         *
         * $commands = [];
         * foreach ($this->commandSets as $commandSet) {
         *     $commands[] = $commandSet->buildCommands();
         * }
         * $commands = flatten($commands);
         */
        $commands = flatten(map($this->commandSets, x(CommandSet::class)->buildCommands()));

        /** @var Command $command */
        foreach ($commands as $command) {
            $app->add($command->getTarget());

            // This isn't supported (yet) and should be done by the end user directly
//            if ($command->isDefault()) {
//                $this->application->setDefaultCommand($command->getFullName());
//            }
        }

        return $app;
    }
}
