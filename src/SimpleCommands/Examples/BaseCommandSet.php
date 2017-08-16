<?php

namespace SimpleCommands\Examples;

use SimpleCommands\Annotations\Command;
use SimpleCommands\Annotations\Option;

class BaseCommandSet
{
    /**
     * @Option()
     *
     * @var string
     */
    protected $workingDirectory = './repositories';

    /**
     * Is repository already loaded? Non-zero status code if not loaded
     *
     * @Command()
     *
     * @param string $url A repository URL
     *
     * @return int
     */
    public function isLoaded($url)
    {
        // Each command can have exit status.
        return 1;
    }
}
