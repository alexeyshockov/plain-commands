<?php

namespace SimpleCommands\Reflection;

use LengthException;
use phpDocumentor\Reflection\DocBlock;
use PhpOption\Option;
use ReflectionMethod;
use function Colada\x;
use function Functional\map;
use function Functional\zip;

class MethodDefinition extends AbstractDefinition
{
    /**
     * @var ReflectionMethod
     */
    private $method;

    /**
     * @var Option<DocBlock>
     */
    private $docBlock;

    /**
     * @param ReflectionMethod $property
     * @param Reflector        $reflector
     */
    public function __construct(ReflectionMethod $property, Reflector $reflector)
    {
        parent::__construct($reflector);

        $this->method = $property;
        $this->docBlock = $reflector->readDocBlock($this->method);
    }

    /**
     * @param string $type
     *
     * @return Option
     */
    public function readAnnotation($type)
    {
        return $this->reflector->readAnnotation($this->method, $type);
    }

    public function getShortDescription(): string
    {
        return $this->docBlock->map(x()->getSummary())->getOrElse('');
    }

    public function getLongDescription(): string
    {
        return $this->docBlock->map(x()->getDescription())->getOrElse('');
    }

    public function getName()
    {
        return $this->method->getName();
    }

    /**
     * @throws LengthException If there is mismatch between @param tags and real parameters
     *
     * @return ParameterDefinition[]
     */
    public function getParameters()
    {
        $parameters = $this->method->getParameters();
        $tags = $this->docBlock->map(x()->getTagsByName('param'))->getOrElse([]);

        if (count($parameters) !== count($tags)) {
            throw new LengthException('Number of parameters is not equal to number of @var tags');
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
        return $this->method->invokeArgs($object, $arguments);
    }
}
