<?php

namespace SimpleCommands\Reflection;

use LengthException;
use phpDocumentor\Reflection\DocBlock;
use PhpOption\Option;
use ReflectionMethod;

use function Functional\map;
use function Functional\zip;

class MethodDefinition extends AbstractDefinition
{
    /**
     * @var ReflectionMethod
     */
    private $method;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @param ReflectionMethod $method
     * @param Reflector $reflector
     */
    public function __construct(ReflectionMethod $method, Reflector $reflector)
    {
        parent::__construct($reflector);

        $this->method = $method;
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
        return $this->method->getName();
    }

    /**
     * @throws LengthException If there is mismatch between "param" tags and real parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        $parameters = $this->method->getParameters();
        $tags = $this->docBlock->getTagsByName("param");

        if (count($parameters) !== count($tags)) {
            throw new LengthException("Parameters number is not equal to @var tags number.");
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
    public function invokeFor($object, $arguments)
    {
        return $this->method->invokeArgs($object, $arguments);
    }
}
