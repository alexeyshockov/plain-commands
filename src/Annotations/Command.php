<?php

namespace PlainCommands\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 *
 * @api
 */
class Command
{
    /**
     * Command name (will be extracted from method name by default)
     *
     * @var string
     */
    public $value;

    /**
     * @var array<string>
     */
    public $shortcuts = [];

    public function getName(): \PhpOption\Option
    {
        return \PhpOption\Option::fromValue($this->value);
    }
}
