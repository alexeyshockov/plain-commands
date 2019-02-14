<?php

namespace PlainCommands\Examples;

use PlainCommands\Annotations\Command;
use PlainCommands\Annotations\Option;

abstract class BaseCommandSet
{
    /**
     * How many parallel HTTP requests are allowed
     *
     * @Option(shortcuts={"c"})
     *
     * @var int
     */
    protected $concurrency = 5;

    // Just a usual field, it's not a command option without @Option annotation
    protected $workingDirectory;

    /**
     * Export path for the command
     *
     * The description from PHPDoc goes nowhere for command options, there is no appropriate place in Symfony for it.
     *
     * @Option()
     *
     * @param string $dir
     */
    public function setWorkingDirectory($dir = './')
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException('Working directory should exist');
        }

        $this->workingDirectory = $dir;
    }

    /**
     * Example option without default value
     *
     * @Option()
     *
     * @param string $something
     */
    // Doesn't work, because the method is PROTECTED
    protected function setSomethingWrong($something)
    {
    }

    /**
     * Is repository already loaded? Exit code 1 if not
     *
     * @Command()
     *
     * @param string $url A repository URL
     *
     * @return int
     */
    public function isLoaded($url)
    {
        // Return value is the exit code, works the same way as in default Symfony Console
        return 1;
    }
}
