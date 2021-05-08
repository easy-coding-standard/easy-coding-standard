<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\DependencyInjection\Config;

use ECSPrefix20210508\Symfony\Component\Config\Resource\ResourceInterface;
use ECSPrefix20210508\Symfony\Component\Config\ResourceCheckerInterface;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class ContainerParametersResourceChecker implements \ECSPrefix20210508\Symfony\Component\Config\ResourceCheckerInterface
{
    /** @var ContainerInterface */
    private $container;
    public function __construct(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * {@inheritdoc}
     */
    public function supports(\ECSPrefix20210508\Symfony\Component\Config\Resource\ResourceInterface $metadata)
    {
        return $metadata instanceof \ECSPrefix20210508\Symfony\Component\DependencyInjection\Config\ContainerParametersResource;
    }
    /**
     * {@inheritdoc}
     * @param int $timestamp
     */
    public function isFresh(\ECSPrefix20210508\Symfony\Component\Config\Resource\ResourceInterface $resource, $timestamp)
    {
        $timestamp = (int) $timestamp;
        foreach ($resource->getParameters() as $key => $value) {
            if (!$this->container->hasParameter($key) || $this->container->getParameter($key) !== $value) {
                return \false;
            }
        }
        return \true;
    }
}
