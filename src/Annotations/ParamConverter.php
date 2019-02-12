<?php

namespace SimpleCommands\Annotations;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
// TODO Support this annotation...
class ParamConverter
{
    /**
     * Parameter name.
     *
     * @Required
     *
     * @var string
     */
    public $value;
}
