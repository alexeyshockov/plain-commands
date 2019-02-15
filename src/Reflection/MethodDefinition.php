<?php

namespace PlainCommands\Reflection;

use LengthException;
use phpDocumentor\Reflection\DocBlock;
use PhpOption\Option;
use ReflectionMethod;
use function Colada\x;
use function Functional\map;
use function Functional\zip;
use Traversable;

class MethodDefinition
{
    use StructuralElement;

    public function __construct(ReflectionMethod $property, Reflector $reflector)
    {
        $this->reflector = $reflector;
        $this->element = $property;
        $this->docBlock = $reflector->readDocBlock($this->element);
    }

    /**
     * @throws LengthException If there is mismatch between @param tags and real parameters
     *
     * @return ParameterDefinition[]
     */
    public function getParameters()
    {
        $parameters = $this->element->getParameters();
        $tags = $this->docBlock->map(x(DocBlock::class)->getTagsByName('param'))->getOrElse([]);

        if (count($parameters) !== count($tags)) {
            throw new LengthException('Number of parameters is not equal to number of @param tags');
        }

        return map(zip($parameters, $tags), function ($data) {
            return new ParameterDefinition($data[0], $data[1], $this->reflector);
        });
    }

    /**
     * @param object $object
     * @param array $arguments
     *
     * @return mixed
     */
    public function invokeFor($object, array $arguments)
    {
        return $this->element->invokeArgs($object, $arguments);
    }
}
