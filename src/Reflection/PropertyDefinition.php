<?php

namespace SimpleCommands\Reflection;

use phpDocumentor\Reflection\DocBlock;
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
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var Option
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
        $this->tag = Option::fromArraysValue($this->docBlock->getTagsByName('var'), 0);
    }

    /**
     * @return Type
     */
    public function getType()
    {
        // In our domain all properties are string by default, if nothing else is specified
        $type = $this->tag->map(x()->getType())->getOrElse('string');

        // TODO Remove duplication with ParameterDefinition::getType()

        $scalarType = ScalarType::create($type);

        $objectType = Option::fromReturn(function () use ($type) {
            try {
                new ObjectType($this->reflector->reflectClass($type));
            } catch (ReflectionException $exception) {
                return null;
            }
        });

        $anyType = new Type($type);

        return $scalarType->orElse($objectType)->getOrElse($anyType);
    }

    /**
     * @param string $type
     *
     * @return Option
     */
    public function readAnnotation($type)
    {
        return $this->reflector->readAnnotation($this->property, $type);
    }

    public function getShortDescription()
    {
        return $this->docBlock->getShortDescription();
    }

    public function getLongDescription()
    {
        return $this->docBlock->getLongDescription();
    }

    public function getName()
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
     */
    public function setValue($object, $value)
    {
        $this->property->setValue($object, $value);
    }
}
