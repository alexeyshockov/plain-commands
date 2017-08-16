<?php

namespace SimpleCommands\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class CommandSet
{
    /**
     * Namespace of command set.
     *
     * @var string
     */
    public $value = "";
}
