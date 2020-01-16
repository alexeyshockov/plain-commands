<?php

namespace PlainCommands;

use Doctrine\Annotations\AnnotationReader as V2AnnotationReader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use PlainCommands\Reflection\Reflector;

/**
 * @api
 */
final class Setup
{
    public static function commandBuilder()
    {
        return new CommandBuilder(new Reflector(self::createReader()));
    }

    private static function createReader()
    {
        if (class_exists(AnnotationRegistry::class)) {
            AnnotationRegistry::registerLoader('class_exists');

            return new CachedReader(new AnnotationReader(), new ArrayCache());
        } else {
            return new V2AnnotationReader();
        }
    }
}
