<?php

namespace PlainCommands\Reflection;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\String_;
use PhpOption\Option;
use ReflectionException;
use ReflectionProperty;

use function Colada\x;

class PropertyDefinition
{
    use StructuralElement;

    /**
     * @var Option<Var_>
     */
    private $tag;

    public function __construct(ReflectionProperty $property, Reflector $reflector)
    {
        $this->reflector = $reflector;

        $this->element = $property;
        $this->docBlock = $reflector->readDocBlock($this->element);

        if (!$this->element->isPublic()) {
            $this->element->setAccessible(true);
        }

        // Try to find @var tag for the property.
        $this->tag = Option::fromArraysValue(
            $this->docBlock->map(x(DocBlock::class)->getTagsByName('var'))->getOrElse([]),
            0
        );
    }

    public function getType(): Type
    {
        return $this->typeFrom($this->tag->map(x(Var_::class)->getType())->getOrElse('string'));
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    public function getValue($object)
    {
        return $this->element->getValue($object);
    }

    /**
     * @param object $object
     * @param mixed  $value
     *
     * @return $this
     */
    public function setValue($object, $value)
    {
        $this->element->setValue($object, $value);

        return $this;
    }
}
