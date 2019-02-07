<?php

namespace SimpleCommands\Examples;

use SimpleCommands\Annotations\Command;
use SimpleCommands\Annotations\Option;

class BaseCommandSet
{
    /**
     * Working directory for the command
     *
     * @Option(shortcuts={"w"})
     *
     * @var string
     */
    protected $workingDirectory = './repositories';

    // Just a usual field, it's not a command option without @Option annotation
    protected $exportPath;

    /**
     * Export path for the command
     *
     * @Option()
     *
     * @param string $exportPath
     */
    protected function setExportPath($exportPath = './')
    {
        if (!is_dir($exportPath)) {
            throw new \InvalidArgumentException('Export path should be an existing directory');
        }

        $this->exportPath = $exportPath;
    }

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
