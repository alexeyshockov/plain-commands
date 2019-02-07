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

    protected $exportPath = './';

    /**
     * @Option()
     *
     * @param string $exportPath
     */
    protected function setExportPath($exportPath)
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
