<?php

namespace PlainCommands\Tests;

use PHPUnit\Framework\TestCase;
use PlainCommands\CommandBuilder;
use PlainCommands\Examples\Commons;
use PlainCommands\Examples\RepositoryGrabber;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ExamplesTest extends TestCase
{
    /**
     * @var CommandBuilder
     */
    private $builder;

    /**
     * @var Application
     */
    private $app;

    function setUp()
    {
        $this->app = new Application();
        $this->app->setAutoExit(false);
        $this->builder = require __DIR__ . '/../examples/bootstrap.php';

        // Basically the same as in examples/app.php
        $this->builder
            ->addCommandsFrom(new RepositoryGrabber())
            ->addCommandsFrom(new Commons())
            ->applyTo($this->app);
    }

    /**
     * @test
     */
    function all_commands_should_be_registered()
    {
        assertTrue($this->app->has('greeting'));
        assertTrue($this->app->has('vcs:load-from-github'));
        assertTrue($this->app->has('vcs:load-from-bitbucket'));
    }

    /**
     * @test
     */
    function namespaced_command_should_work()
    {
        $command = $this->app->find('vcs:load-from-github');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => 'vcs:load-from-github',

            'url' => 'https://github.com/alexeyshockov/plain-commands',
            '--working-directory' => '/tmp',
            '--verify-ssl' => null,
        ], ['capture_stderr_separately' => true]);

        assertEquals(
            "https://github.com/alexeyshockov/plain-commands will be cloned to /tmp (with SSL)\n",
            $commandTester->getDisplay()
        );
    }
}
