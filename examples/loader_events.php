#!/usr/bin/env php
<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use SimpleCommands\CommandBuilder;
use SimpleCommands\Examples\RepositoryGrabber;
use SimpleCommands\Reflection\Reflector;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

$classLoader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$classLoader, 'loadClass']);

$reflector = new Reflector();
$application = new Application();

// Events.
$dispatcher = new EventDispatcher();
$application->setDispatcher($dispatcher);
$dispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {
    $output = $event->getOutput();
    $command = $event->getCommand();
    $output->writeln(sprintf('After running command <info>%s</info>', $command->getName()));
    $event->setExitCode(128);
});

// Wrap application.
(new CommandBuilder($application, $reflector))
    ->addCommandsFrom(new RepositoryGrabber())
;

$application->run();
