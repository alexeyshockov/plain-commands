<?php

namespace PlainCommands\Arguments;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait OutputInterfaceProxy
{
    /**
     * @var OutputInterface
     */
    protected $out;

    public function write($messages, $newline = false, $options = 0)
    {
        return $this->out->write($messages, $newline, $options);
    }

    public function writeln($messages, $options = 0)
    {
        return $this->out->writeln($messages, $options);
    }

    public function setVerbosity($level)
    {
        return $this->out->setVerbosity($level);
    }

    public function getVerbosity()
    {
        return $this->out->getVerbosity();
    }

    public function isQuiet()
    {
        return $this->out->isQuiet();
    }

    public function isVerbose()
    {
        return $this->out->isVerbose();
    }

    public function isVeryVerbose()
    {
        return $this->out->isVeryVerbose();
    }

    public function isDebug()
    {
        return $this->out->isDebug();
    }

    public function setDecorated($decorated)
    {
        return $this->out->setDecorated($decorated);
    }

    public function isDecorated()
    {
        return $this->out->isDecorated();
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
        return $this->out->setFormatter($formatter);
    }

    public function getFormatter()
    {
        return $this->out->getFormatter();
    }
}
