#!/usr/bin/env php
<?php

use PlainCommands\CommandBuilder;
use PlainCommands\Examples\RepositoryGrabber;
use Symfony\Component\Console\Application;

/** @var CommandBuilder $builder */
$builder = require __DIR__ . '/bootstrap.php';

$builder
    ->addCommandsFrom(new RepositoryGrabber())
    ->applyTo(new Application())
    ->run()
;
