<?php

namespace PlainCommands\Examples;

use PlainCommands\Annotations\Command;
use PlainCommands\Annotations\CommandSet;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command sets are usual POPO (Plain Old PHP Objects) and can extend like other classes.
 *
 * @CommandSet("vcs")
 */
class RepositoryGrabber extends BaseCommandSet
{
    /**
     * Load a repository from GitHub (this summary goes to the command's description)
     *
     * And the description from PHPDoc goes to "Help" section of command's --help output.
     *
     * @Command(shortcuts={"g"})
     *
     * @param OutputInterface $writer
     * @param string          $url A repository URL
     * @param bool            $verifySsl Verify SSL?
     */
    public function loadFromGithub(OutputInterface $writer, $url, $verifySsl = false)
    {
        $ssl = $verifySsl ? 'with SSL' : 'without SSL';

        $writer->writeln("$url will be cloned to {$this->workingDirectory} ($ssl)");

        // Default exit code is 0 (as in default Symfony Console), that means everything is OK
    }

    /**
     * Load a repository from BitBucket
     *
     * @Command(shortcuts={"b"})
     *
     * @param OutputInterface $writer
     * @param string          $url A repository URL
     */
    public function loadFromBitbucket(OutputInterface $writer, $url)
    {
        $writer->writeln("$url will be cloned to {$this->workingDirectory}");
    }

    // This method is not a command, it doesn't use @Command annotation
    public function arbitraryMethod()
    {
    }
}
