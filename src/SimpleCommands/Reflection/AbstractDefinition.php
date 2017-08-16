<?php

namespace SimpleCommands\Reflection;

class AbstractDefinition
{
    /**
     * @var Reflector
     */
    protected $reflector;

    /**
     * @param Reflector $reflector
     */
    public function __construct(Reflector $reflector)
    {
        $this->reflector = $reflector;
    }
}
