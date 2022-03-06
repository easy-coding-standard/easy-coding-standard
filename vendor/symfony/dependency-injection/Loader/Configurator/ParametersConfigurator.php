<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20220306\Symfony\Component\DependencyInjection\Loader\Configurator;

use ECSPrefix20220306\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20220306\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ECSPrefix20220306\Symfony\Component\ExpressionLanguage\Expression;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ParametersConfigurator extends \ECSPrefix20220306\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    public const FACTORY = 'parameters';
    private $container;
    public function __construct(\ECSPrefix20220306\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $this->container = $container;
    }
    /**
     * @return $this
     * @param mixed $value
     */
    public final function set(string $name, $value)
    {
        if ($value instanceof \ECSPrefix20220306\Symfony\Component\ExpressionLanguage\Expression) {
            throw new \ECSPrefix20220306\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Using an expression in parameter "%s" is not allowed.', $name));
        }
        $this->container->setParameter($name, static::processValue($value, \true));
        return $this;
    }
    /**
     * @return $this
     * @param mixed $value
     */
    public final function __invoke(string $name, $value)
    {
        return $this->set($name, $value);
    }
}
