<?php

namespace SimpleCommands\Reflection;

use InvalidArgumentException;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

/**
 * Integer, float, string or boolean
 */
class ScalarType extends Type
{
    const NAMES = ['integer', 'int', 'float', 'double', 'string', 'boolean', 'bool'];

    /**
     * "Silent" version of constructor (optional return value instead of an exception)
     *
     * @param string $name
     *
     * @return Option
     */
    public static function create(string $name)
    {
        try {
            return new Some(new static($name));
        } catch (InvalidArgumentException $exception) {
            return None::create();
        }
    }

    public function __construct(string $name)
    {
        parent::__construct($name);

        if (!in_array($this->name, self::NAMES)) {
            throw new InvalidArgumentException("\"$name\" is not a valid scalar type");
        }
    }
}
