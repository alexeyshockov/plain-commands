# simple-commands

Create cli applications with many commands easily. This library is a lightweight wrapper around Symfony Console 
Component, that can be used alone or within Symfony based web application.

## Example

Application :
``` php
#!/usr/bin/env php
<?php

use DataLoader\CommandSet;
use DataLoader\Stopwatch;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\DriverManager;
use SimpleCommands\Application;

$loader = require __DIR__.'/vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$stopwatch = new Stopwatch();

// TODO Application name - filename.
$console = new Application();
$console->addCommandSet(new CommandSet($dbConnection));
```

And command set class:
``` php
class CommandSet
{
    /**
     * @var string
     */
    private $outputDirectory;
    
    /**
     * "Interface" method for options. Options by idea are fields, already initialized with values, which can be
     * overwritten by a user.
     *
     * @param string $outputDirectory Working directory (current directory by default).
     */
    public function setOptions($outputDirectory = "./processed")
    {
        $this->outputDirectory = $outputDirectory;
    }

    /**
     * Concatenate given file to one file and write it to output directory. 
     *
     * This is the long usage description.
     *
     * @param array $files Files to process (separated by a space).
     * @param Output $writer
     */
    public function concatenateCommand(array $files, Output $writer)
    {
        // Processing...

        $writer("Done! Files processed: %d.", count($files));
    }
}
```

## Roadmap

1. Options support.
2. @CommandSet and @Command support.
3. SimpleCommands\Reflection\* to interfaces?
4. ...
