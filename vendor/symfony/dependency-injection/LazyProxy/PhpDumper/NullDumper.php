<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper;

use ECSPrefix20210507\Symfony\Component\DependencyInjection\Definition;
/**
 * Null dumper, negates any proxy code generation for any given service definition.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @final
 */
class NullDumper implements \ECSPrefix20210507\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\DumperInterface
{
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\Definition $definition
     * @return bool
     */
    public function isProxyCandidate($definition)
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\Definition $definition
     * @param string $id
     * @param string $factoryCode
     * @return string
     */
    public function getProxyFactoryCode($definition, $id, $factoryCode)
    {
        return '';
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\Definition $definition
     * @return string
     */
    public function getProxyCode($definition)
    {
        return '';
    }
}
