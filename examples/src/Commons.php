<?php

namespace PlainCommands\Examples;

use PlainCommands\Annotations\Command;
use PlainCommands\Arguments\StdOut;

// Without @CommandSet annotation all the commands will have no namespace
class Commons
{
    /**
     * @Command()
     *
     * @param StdOut $stdout
     * @param string $name
     * @param int    $age
     */
    public function greeting(StdOut $stdout, string $name, int $age)
    {
        $stdout('Hello, %s! Your age is: %d', $name, $age);

        // The default exit code is 0, that means everything is OK.
    }
}
