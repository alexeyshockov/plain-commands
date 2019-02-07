<?php

namespace SimpleCommands;

use InvalidArgumentException;
use SimpleCommands\Reflection\Reflector;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

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
     * @param object $object
     *
     * @return static
     */
    public function addCommandsFrom($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Only objects are supported.');
        }

        $commandSet = new CommandSet($this->reflector->reflectObject($object));

        foreach ($commandSet->buildCommands() as $command) {
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
}
