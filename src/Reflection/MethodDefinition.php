<?php

namespace PlainCommands\Reflection;

use LengthException;
use phpDocumentor\Reflection\DocBlock;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
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
     * @return Traversable<ParameterDefinition>
     */
    public function getParameters()
    {
        $parameters = $this->element->getParameters();
        $tags = $this->docBlock->map(x(DocBlock::class)->getTagsByName('param'))->getOrElse([]);

        foreach (zip($parameters, $tags) as list($p, $t)) {
            yield new ParameterDefinition($this->reflector, $p, $t);
        }
    }

    public function getParameter(): Option
    {
        if ($this->element->getNumberOfParameters() > 0) {
            foreach ($this->getParameters() as $parameter) {
                return new Some($parameter);
            }
        }

        return None::create();
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
