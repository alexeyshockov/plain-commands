<?php

namespace SimpleCommands\Reflection;

/**
 * All other types, not needed for this library.
 */
class UnknownType extends Type
{
    public function __construct($name = null)
    {
        parent::__construct($name ?: "mixed");
    }
}
