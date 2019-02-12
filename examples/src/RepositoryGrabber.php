<?php

namespace SimpleCommands\Examples;

use SimpleCommands\Annotations\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command sets are usual POPO (Plain Old PHP Objects) and can extend like other classes.
 */
class RepositoryGrabber extends BaseCommandSet
{
    /**
     * Load a repository from GitHub
     *
     * Long desc
     *
     * @Command(shortcuts={"gh", "g"})
     *
     * @param OutputInterface $writer
     * @param string          $url A repository URL
     * @param bool            $verifySsl Verify SSL?
     */
    public function loadFromGithub(OutputInterface $writer, $url, $verifySsl = false)
    {
        var_dump($url, $verifySsl);

        var_dump($this->workingDirectory);
        var_dump($this->exportPath);

        $writer->writeln('Yes, we are here!');

        // The default exit code is 0, that means everything is OK.
    }

    /**
     * Load repository from BitBucket
     *
     * @Command(shortcuts={"bb", "b"})
     *
     * @param OutputInterface $writer
     * @param string          $url A repository URL
     */
    public function loadFromBitbucket(OutputInterface $writer, $url)
    {

    }

    // This method is not a command, it doesn't use @Command annotation
    public function arbitraryMethod()
    {

    }
}
