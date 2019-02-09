#!/usr/bin/env php
<?php

use SimpleCommands\CommandBuilder;
use SimpleCommands\Examples\RepositoryGrabber;
use Symfony\Component\Console\Application;

/** @var CommandBuilder $builder */
$builder = require __DIR__ . '/bootstrap.php';

$builder
    ->addCommandsFrom(new RepositoryGrabber())
    ->applyTo(new Application())
    // See https://symfony.com/doc/current/components/console/single_command_tool.html for details.
    ->setDefaultCommand('load-from-github', true)
    ->run()
;
