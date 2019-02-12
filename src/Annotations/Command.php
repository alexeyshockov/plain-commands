<?php

namespace PlainCommands\Annotations;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Command
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

    /**
     * @var bool
     */

    // This option is not supported, because it introduces unneeded complexity (for debugging). Default command (in
    // most cases) should be defined only once in the same place where the app is defined.
//    public $default = false;
}
