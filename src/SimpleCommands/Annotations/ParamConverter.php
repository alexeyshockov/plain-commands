<?php

namespace SimpleCommands\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

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
