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
        $type = new UnknownType($this->tag->getType());
        if ($this->parameter->isArray()) {
            $type = new ArrayType();
        } elseif ($this->parameter->getClass()) {
            $type = new ObjectType($this->reflector->reflectClass($this->parameter->getClass()));
        }

        return $type;
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
