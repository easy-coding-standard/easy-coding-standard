<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\LazyProxy\PhpDumper;

use Symfony\Component\DependencyInjection\Definition;

/**
 * Null dumper, negates any proxy code generation for any given service definition.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 *
 * @final
 */
class NullDumper implements DumperInterface
{
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isProxyCandidate(Definition $definition)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     * @param string $id
     * @param string $factoryCode
     * @return string
     */
    public function getProxyFactoryCode(Definition $definition, $id, $factoryCode)
    {
        $id = (string) $id;
        $factoryCode = (string) $factoryCode;
        return '';
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getProxyCode(Definition $definition)
    {
        return '';
    }
}
