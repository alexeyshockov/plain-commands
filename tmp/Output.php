<?php

namespace SimpleCommands;

use Symfony\Component\Console\Output\OutputInterface;

// TODO Implement OutputInterface (be a decorator)?..
class Output
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function __invoke(...$arguments)
    {
        $line = count($arguments) ? sprintf(...$arguments) : '';
        $this->output->writeln($line);
    }
}
