<?php

use Doctrine\Annotations\AnnotationReader as V2AnnotationReader;
use Doctrine\Common\Annotations\AnnotationReader as V1AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use SimpleCommands\CommandBuilder;
use SimpleCommands\Reflection\Reflector;

$classLoader = require __DIR__ . '/../vendor/autoload.php';
if (class_exists(AnnotationRegistry::class)) {
    AnnotationRegistry::registerLoader('class_exists');

    $annotationReader = new V1AnnotationReader();
} else {
    $annotationReader = new V2AnnotationReader();
}

$reflector = new Reflector($annotationReader);

return new CommandBuilder($reflector);
