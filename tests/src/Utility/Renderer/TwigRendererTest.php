<?php

namespace Maverick\Utility\Renderer;

use PHPUnit_Framework_TestCase;
use Twig_Environment;

/**
 * @coversDefaultClass Maverick\Utility\Renderer\TwigRenderer
 */
class TwigRendererTest extends PHPUnit_Framework_TestCase
{
    protected function getMockTwigEnvironment()
    {
        return $this->getMockBuilder(Twig_Environment::class)
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsEnvironment()
    {
        $given = $expected = $this->getMockTwigEnvironment();

        $instance = new TwigRenderer($given);

        $this->assertAttributeSame($expected, 'twig', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsFileSuffix()
    {
        $env = $this->getMockTwigEnvironment();

        $given = $expected = 'html.twig';

        $instance = new TwigRenderer($env, $given);

        $this->assertAttributeSame($expected, 'suffix', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testDotsTrimmedFromSuffixBeginningAndEnd()
    {
        $env = $this->getMockTwigEnvironment();

        $given = '.suffix.type.';
        $expected = 'suffix.type';

        $instance = new TwigRenderer($env, $given);

        $this->assertAttributeSame($expected, 'suffix', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testFileSuffixDefaultsToTwig()
    {
        $env = $this->getMockTwigEnvironment();

        $expected = 'twig';

        $instance = new TwigRenderer($env);

        $this->assertAttributeSame($expected, 'suffix', $instance);
    }

    /**
     * @covers ::render
     */
    public function testRenderLoadsNamedTemplateWithVariables()
    {
        $suffix = 'twig';

        $given = 'test.template';
        $expected = $given . '.' . $suffix;

        $variables = ['this' => 'is', 'a' => 'variable'];

        $output = 'the rendered template';

        $env = $this->getMockTwigEnvironment();

        $env->expects($this->once())
            ->method('render')
            ->with($expected, $variables)
            ->willReturn($output);

        $instance = new TwigRenderer($env, $suffix);

        $this->assertEquals($output, $instance->render($given, $variables));
    }
}
