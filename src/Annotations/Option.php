<?php

namespace PlainCommands\Annotations;

/**
 * @Annotation
 * @Target({"METHOD", "PROPERTY"})
 *
 * @api
 */
class Option
{
    /**
     * Command name. Will be extracted from method name by default.
     *
     * @var string
     */
    public $value;

    /**
     * @var array<string>
     */
    public $shortcuts = [];
}
