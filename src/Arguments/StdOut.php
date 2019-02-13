<?php

namespace PlainCommands\Arguments;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @api
 */
class StdOut implements OutputInterface
{
    use OutputInterfaceProxy;

    public function __construct(OutputInterface $out)
    {
        $this->out = $out;
    }

    public function __invoke(...$arguments)
    {
        $line = count($arguments) ? sprintf(...$arguments) : '';
        $this->out->writeln($line);
    }
}
