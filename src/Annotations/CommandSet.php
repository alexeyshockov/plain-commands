<?php

namespace PlainCommands\Annotations;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class CommandSet
{
    /**
     * Namespace for the command set
     *
     * @var string
     */
    public $value = '';
}
