#!/usr/bin/env php
<?php

use PlainCommands\CommandBuilder;
use PlainCommands\Examples\Commons;
use PlainCommands\Examples\RepositoryGrabber;
use Symfony\Component\Console\Application;

/** @var CommandBuilder $builder */
$builder = require __DIR__ . '/bootstrap.php';

$builder
    ->addCommandsFrom(new RepositoryGrabber())
    ->addCommandsFrom(new Commons())
    ->applyTo(new Application())
    // See https://symfony.com/doc/current/components/console/single_command_tool.html for details.
    ->setDefaultCommand('vcs:load-from-github', true)
    ->run()
;
