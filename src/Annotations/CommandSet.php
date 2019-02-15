<?php

namespace PlainCommands\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @api
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
