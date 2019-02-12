<?php

namespace SimpleCommands\Reflection;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\String_;
use PhpOption\Option;
use ReflectionException;
use ReflectionProperty;
use function Colada\x;

class PropertyDefinition extends AbstractDefinition
{
    /**
     * @var ReflectionProperty
     */
    private $property;

    /**
     * @var Option<DocBlock>
     */
    private $docBlock;

    /**
     * @var Option<Var_>
     */
    private $tag;

    public function __construct(ReflectionProperty $property, Reflector $reflector)
    {
        parent::__construct($reflector);

        $this->property = $property;
        $this->docBlock = $reflector->readDocBlock($this->property);

        if (!$this->property->isPublic()) {
            $this->property->setAccessible(true);
        }

        // Try to find @var tag for the property.
        $this->tag = Option::fromArraysValue(
            $this->docBlock->map(x()->getTagsByName('var'))->getOrElse([]),
            0
        );
    }

    public function getType(): Type
    {
        // In our domain all properties are string by default, if nothing else is specified
        $type = (string) $this->tag->map(x()->getType())->getOrElse(new String_());

        // TODO Remove duplication with ParameterDefinition::getType()

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

    public function readAnnotation(string $type): Option
    {
        return $this->reflector->readAnnotation($this->property, $type);
    }

    public function getShortDescription(): string
    {
        return $this->docBlock->map(x()->getSummary())->getOrElse('');
    }

    public function getLongDescription(): string
    {
        return $this->docBlock->map(x()->getDescription())->getOrElse('');
    }

    public function getName(): string
    {
        return $this->property->getName();
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getValue($object)
    {
        return $this->property->getValue($object);
    }

    /**
     * @param object $object
     * @param mixed  $value
     *
     * @return $this
     */
    public function setValue($object, $value)
    {
        $this->property->setValue($object, $value);

        return $this;
    }
}
