<?php

namespace PlainCommands\Reflection;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use ReflectionException;
use ReflectionParameter;

class ParameterDefinition extends AbstractDefinition
{
    /**
     * @var ReflectionParameter
     */
    private $parameter;

    /**
     * @var Param
     */
    private $tag;

    public function __construct(ReflectionParameter $parameter, Param $tag, Reflector $reflector)
    {
        parent::__construct($reflector);

        $this->parameter = $parameter;
        $this->tag = $tag;
    }

    public function getType(): Type
    {
        // ReflectionType::getName() is available only from PHP 7.1.0
        $type = $this->parameter->hasType() ? $this->parameter->getType()->getName() : (string) $this->tag->getType();

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

    public function getName(): string
    {
        return $this->parameter->getName();
    }

    public function getDescription(): string
    {
        return $this->tag->getDescription();
    }

    public function hasDefaultValue(): bool
    {
        return $this->getDefaultValue()->isDefined();
    }

    public function getDefaultValue(): Option
    {
        return $this->parameter->isDefaultValueAvailable()
            ? new Some($this->parameter->getDefaultValue())
            : None::create();
    }
}
