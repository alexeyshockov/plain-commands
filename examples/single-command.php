#!/usr/bin/env php
<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use SimpleCommands\CommandBuilder;
use SimpleCommands\Examples\RepositoryGrabber;
use SimpleCommands\Reflection\Reflector;
use Symfony\Component\Console\Application;

$classLoader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$classLoader, 'loadClass']);

$application = new Application();

// Wrap application.
(new CommandBuilder($application, new Reflector()))
    ->addCommandsFrom(new RepositoryGrabber())
;

// See https://symfony.com/doc/current/components/console/single_command_tool.html for details.
$application->setDefaultCommand('load-from-github', true);

$application->run();
