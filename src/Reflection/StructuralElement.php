<?php

namespace PlainCommands\Reflection;

use phpDocumentor\Reflection\DocBlock;
use PhpOption\Option;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use function Colada\x;

trait StructuralElement
{
    use Definition;

    /**
     * @var ReflectionProperty|ReflectionMethod|ReflectionClass
     */
    protected $element;

    /**
     * @var Option<DocBlock>
     */
    protected $docBlock;

    public function readAnnotation(string $type): Option
    {
        return $this->reflector->readAnnotation($this->element, $type);
    }

    public function getShortDescription(): string
    {
        return $this->docBlock->map(x(DocBlock::class)->getSummary())->getOrElse('');
    }

    public function getLongDescription(): string
    {
        return $this->docBlock->map(x(DocBlock::class)->getDescription()->render())->getOrElse('');
    }

    public function getName(): string
    {
        return $this->element->getName();
    }
}
