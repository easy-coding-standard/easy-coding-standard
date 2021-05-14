<?php

namespace ECSPrefix20210514\Symplify\PackageBuilder\Parameter;

use ECSPrefix20210514\Symfony\Component\DependencyInjection\Container;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
/**
 * @see \Symplify\PackageBuilder\Tests\Parameter\ParameterProviderTest
 */
final class ParameterProvider
{
    /**
     * @var array<string, mixed>
     */
    private $parameters = [];
    /**
     * @param Container|ContainerInterface $container
     */
    public function __construct(\ECSPrefix20210514\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $parameterBag = $container->getParameterBag();
        $this->parameters = $parameterBag->all();
    }
    /**
     * @param string $name
     * @return bool
     */
    public function hasParameter($name)
    {
        $name = (string) $name;
        return isset($this->parameters[$name]);
    }
    /**
     * @api
     * @return mixed|null
     * @param string $name
     */
    public function provideParameter($name)
    {
        $name = (string) $name;
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }
    /**
     * @api
     * @param string $name
     * @return string
     */
    public function provideStringParameter($name)
    {
        $name = (string) $name;
        $this->ensureParameterIsSet($name);
        return (string) $this->parameters[$name];
    }
    /**
     * @api
     * @return mixed[]
     * @param string $name
     */
    public function provideArrayParameter($name)
    {
        $name = (string) $name;
        $this->ensureParameterIsSet($name);
        return $this->parameters[$name];
    }
    /**
     * @api
     * @param string $parameterName
     * @return bool
     */
    public function provideBoolParameter($parameterName)
    {
        $parameterName = (string) $parameterName;
        return isset($this->parameters[$parameterName]) ? $this->parameters[$parameterName] : \false;
    }
    /**
     * @return void
     * @param string $name
     */
    public function changeParameter($name, $value)
    {
        $name = (string) $name;
        $this->parameters[$name] = $value;
    }
    /**
     * @api
     * @return mixed[]
     */
    public function provide()
    {
        return $this->parameters;
    }
    /**
     * @api
     * @param string $name
     * @return int
     */
    public function provideIntParameter($name)
    {
        $name = (string) $name;
        $this->ensureParameterIsSet($name);
        return (int) $this->parameters[$name];
    }
    /**
     * @api
     * @return void
     * @param string $name
     */
    public function ensureParameterIsSet($name)
    {
        $name = (string) $name;
        if (\array_key_exists($name, $this->parameters)) {
            return;
        }
        throw new \ECSPrefix20210514\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException($name);
    }
}
