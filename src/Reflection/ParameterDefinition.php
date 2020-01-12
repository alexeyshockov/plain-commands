<?php

namespace PlainCommands\Reflection;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use ReflectionException;
use ReflectionParameter;

use function Colada\x;

class ParameterDefinition
{
    use Definition;

    /**
     * @var ReflectionParameter
     */
    private $element;

    /**
     * @var Option<Param>
     */
    private $tag;

    public function __construct(Reflector $reflector, ReflectionParameter $parameter, Param $tag = null)
    {
        $this->reflector = $reflector;
        $this->element = $parameter;
        $this->tag = Option::fromValue($tag);
    }

    public function getType(): Type
    {
        $phpDocType = $this->tag->map(x(Param::class)->getType());

        $phpType = None::create();
        if ($this->element->hasType()) {
            if (method_exists($t = $this->element->getType(), 'getName')) {
                // ReflectionType::getName() is available only from PHP 7.1.0...
                $phpType = new Some($t->getName());
            } else {
                // ReflectionType::__toString() is deprecated in PHP 7.1.0+ and will be removed in PHP 8
                $phpType = new Some((string) $t);
            }
        }

        return $this->typeFrom($phpType->orElse($phpDocType)->getOrElse('string'));
    }

    public function getName(): string
    {
        return $this->element->getName();
    }

    public function getDescription(): string
    {
        return $this->tag->map(x(Param::class)->getDescription()->render())->getOrElse('');
    }

    public function hasDefaultValue(): bool
    {
        return $this->getDefaultValue()->isDefined();
    }

    public function getDefaultValue(): Option
    {
        try {
            return new Some($this->element->getDefaultValue());
        } catch (ReflectionException $exception) {
            return None::create();
        }
    }
}
