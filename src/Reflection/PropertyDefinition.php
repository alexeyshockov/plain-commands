<?php

namespace SimpleCommands\Reflection;

use function Colada\x;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use PhpOption\Option;
use ReflectionProperty;

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

    public function isArrayType()
    {
        $types = $this->tag->map(x()->getTypes())->getOrElse([]);

        // TODO string[], int[] and so on
        return in_array('array', $types);
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
