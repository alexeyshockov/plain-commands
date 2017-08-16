<?php

namespace spec\SimpleCommands;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Doctrine\Common\Annotations\AnnotationRegistry;
use SimpleCommands\Reflection\Reflector;
use SimpleCommands\CommandBuilder;

use SimpleCommands\Examples\RepositoryGrabber;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

/**
 * @mixin CommandBuilder
 */
class CommandBuilderSpec extends ObjectBehavior
{
    function let(Application $application)
    {
        // Set up annotation reader...
        $loader = require __DIR__ . '/../../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);

        $reflector = new Reflector();

        $this->beConstructedWith($application, $reflector);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('SimpleCommands\CommandBuilder');
    }

    function it_supports_commands_with_postfixes($application, Command $target)
    {
        $application->register(Argument::any())->willReturn($target);

        $application->setDefaultCommand('load-from-github')->shouldBeCalled();

        $this->addCommandsFrom(new RepositoryGrabber())->shouldReturn($this);
    }

    function it_supports_commands_with_annotations()
    {

    }

    function it_supports_empty_options()
    {
        // With type "null" in @param.
    }
}
