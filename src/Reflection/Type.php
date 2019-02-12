<?php

namespace PlainCommands\Reflection;

class Type
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name = 'mixed')
    {
        $this->name = strtolower($name) ?: 'mixed';

        // TODO What about invalid types?..
    }

    public function isBoolean()
    {
        return in_array($this->name, ['boolean', 'bool']);
    }

    public function isArray()
    {
        return $this->name === 'array';
    }

    public function __toString()
    {
        return $this->name;
    }
}
