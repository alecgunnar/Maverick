<?php
/**
 * Maverick
 *
 * @author Alec Carpenter <gunnar94@me.com>
 */

use Symfony\Component\Config\FileLocator;
use Maverick\Loader\YamlConfigLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * @covers Maverick\Loader\YamlConfigLoader
 */
class YamlConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    const CLASS_NAME  = '\\Maverick\\Loader\\YamlConfigLoader';

    protected function getInstance($fileLocator=null)
    {
        return new YamlConfigLoader($fileLocator ?: new FileLocator(TEST_PATH . DIRECTORY_SEPARATOR . 'config'));
    }

    /**
     * @covers Maverick\Loader\YamlConfigLoader::load
     */
    public function testLoadAppendsYamlSuffixIfItsNotPresent()
    {
        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocator')->getMock();

        $locator->expects($this->once())
            ->method('locate')
            ->with('test.yml');

        $this->getInstance($locator)->load('test');
    }

    /**
     * @covers Maverick\Loader\YamlConfigLoader::load
     */
    public function testLoadDoesNotAppendYamlSuffixIfItsPresent()
    {
        $locator = $this->getMockBuilder('Symfony\Component\Config\FileLocator')->getMock();

        $locator->expects($this->once())
            ->method('locate')
            ->with('test.yml');

        $this->getInstance($locator)->load('test.yml');
    }

    /**
     * @covers Maverick\Loader\YamlConfigLoader::load
     */
    public function testLoadFileWhichExists()
    {
        $path = TEST_PATH . DIRECTORY_SEPARATOR . 'config';
        $file = $path . DIRECTORY_SEPARATOR . 'test.yml';

        $loader = $this->getInstance(new FileLocator($path));

        $this->assertEquals(Yaml::parse(file_get_contents($file)), $loader->load('test'));
    }

    /**
     * @covers Maverick\Loader\YamlConfigLoader::load
     * @expectedException InvalidArgumentException
     */
    public function testLoadFileWhichDoesNotExist()
    {
        $this->getInstance()->load('does-not-exist');
    }

    /**
     * @covers Maverick\Loader\YamlConfigLoader::supports
     */
    public function testSupports()
    {
        $instance = $this->getInstance();

        $this->assertTrue($instance->supports('file.' . YamlConfigLoader::FILE_EXT));
        $this->assertFalse($instance->supports('file.xml'));
    }
}