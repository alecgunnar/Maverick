<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Container;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class StandardContainer extends ContainerBuilder implements ContainerInterface
{
    const CONFIG_FILE_NAME = 'services.yml';

    public function __construct(FileLocatorInterface $locator)
    {
        parent::__construct();

        $this->load($locator);
    }

    public static function create(FileLocatorInterface $locator)
    {
        return new self($locator);
    }

    public function extend(FileLocatorInterface $locator)
    {
        $this->load($locator);
    }

    protected function load(FileLocatorInterface $locator)
    {
        (new YamlFileLoader($this, $locator))->load(self::CONFIG_FILE_NAME);
    }
}