<?php

namespace PlainCommands\Reflection;

use PhpOption\Option;
use ReflectionException;

trait Definition
{
    /**
     * @var Reflector
     */
    protected $reflector;

    protected function typeFrom($type): Type
    {
        $type = (string) $type;

        $scalarType = ScalarType::create($type);
        $objectType = Option::fromReturn(function () use ($type) {
            try {
                return new ObjectType($this->reflector->reflectClass($type));
            } catch (ReflectionException $exception) {
                return null;
            }
        });
        $anyType = new Type($type);

        return $scalarType->orElse($objectType)->getOrElse($anyType);
    }
}
