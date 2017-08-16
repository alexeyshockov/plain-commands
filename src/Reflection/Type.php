<?php

namespace SimpleCommands\Reflection;

abstract class Type
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = strtolower($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
