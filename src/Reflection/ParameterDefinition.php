<?php

namespace SimpleCommands\Reflection;

use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use ReflectionParameter;

class ParameterDefinition extends AbstractDefinition
{
    /**
     * @var ReflectionParameter
     */
    private $parameter;

    /**
     * @var ParamTag
     */
    private $tag;

    /**
     * @param ReflectionParameter $parameter
     * @param ParamTag $tag
     * @param Reflector $reflector
     */
    public function __construct(ReflectionParameter $parameter, ParamTag $tag, Reflector $reflector)
    {
        parent::__construct($reflector);

        $this->parameter = $parameter;
        $this->tag = $tag;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        // ReflectionType::getName() is available only from PHP 7.1.0
        $type = $this->parameter->hasType() ? $this->parameter->getType()->getName() : $this->tag->getType();

        $scalarType = ScalarType::create($type);

        $objectType = Option::fromReturn(function () {
            // TODO Support class from PHPDoc?..
            return ($class = $this->parameter->getClass()) // Returns ReflectionClass object or NULL if none
                ? new ObjectType($this->reflector->reflectClass($class))
                : null;
        });

        $anyType = new Type($type);

        return $scalarType->orElse($objectType)->getOrElse($anyType);
    }

    public function getName()
    {
        return $this->parameter->getName();
    }

    public function getDescription()
    {
        return $this->tag->getDescription();
    }

    /**
     * @return bool
     */
    public function hasDefaultValue()
    {
        return $this->getDefaultValue()->isDefined();
    }

    /**
     * @return Option
     */
    public function getDefaultValue()
    {
        $value = None::create();
        if ($this->parameter->isDefaultValueAvailable()) {
            $value = new Some($this->parameter->getDefaultValue());
        }

        return $value;
    }
}
