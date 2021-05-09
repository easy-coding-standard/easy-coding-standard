<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210509\Symfony\Contracts\Service;

use ECSPrefix20210509\Psr\Container\ContainerExceptionInterface;
use ECSPrefix20210509\Psr\Container\NotFoundExceptionInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(\ECSPrefix20210509\Psr\Container\ContainerExceptionInterface::class);
\class_exists(\ECSPrefix20210509\Psr\Container\NotFoundExceptionInterface::class);
/**
 * A trait to help implement ServiceProviderInterface.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait ServiceLocatorTrait
{
    private $factories;
    private $loading = [];
    private $providedTypes;
    /**
     * @param callable[] $factories
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }
    /**
     * {@inheritdoc}
     *
     * @return bool
     * @param string $id
     */
    public function has($id)
    {
        return isset($this->factories[$id]);
    }
    /**
     * {@inheritdoc}
     *
     * @return mixed
     * @param string $id
     */
    public function get($id)
    {
        if (!isset($this->factories[$id])) {
            throw $this->createNotFoundException($id);
        }
        if (isset($this->loading[$id])) {
            $ids = \array_values($this->loading);
            $ids = \array_slice($this->loading, \array_search($id, $ids));
            $ids[] = $id;
            throw $this->createCircularReferenceException($id, $ids);
        }
        $this->loading[$id] = $id;
        try {
            return $this->factories[$id]($this);
        } finally {
            unset($this->loading[$id]);
        }
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function getProvidedServices()
    {
        if (null === $this->providedTypes) {
            $this->providedTypes = [];
            foreach ($this->factories as $name => $factory) {
                if (!\is_callable($factory)) {
                    $this->providedTypes[$name] = '?';
                } else {
                    $type = (new \ReflectionFunction($factory))->getReturnType();
                    $this->providedTypes[$name] = $type ? ($type->allowsNull() ? '?' : '') . ($type instanceof \ReflectionNamedType ? $type->getName() : $type) : '?';
                }
            }
        }
        return $this->providedTypes;
    }
    /**
     * @param string $id
     * @return \Psr\Container\NotFoundExceptionInterface
     */
    private function createNotFoundException($id)
    {
        $id = (string) $id;
        if (!($alternatives = \array_keys($this->factories))) {
            $message = 'is empty...';
        } else {
            $last = \array_pop($alternatives);
            if ($alternatives) {
                $message = \sprintf('only knows about the "%s" and "%s" services.', \implode('", "', $alternatives), $last);
            } else {
                $message = \sprintf('only knows about the "%s" service.', $last);
            }
        }
        if ($this->loading) {
            $message = \sprintf('The service "%s" has a dependency on a non-existent service "%s". This locator %s', \end($this->loading), $id, $message);
        } else {
            $message = \sprintf('Service "%s" not found: the current service locator %s', $id, $message);
        }
        return new \ECSPrefix20210509\Symfony\Contracts\Service\Anonymous__3e88683f5fba080472fe4fa460352f72__0($message);
    }
    /**
     * @param string $id
     * @return \Psr\Container\ContainerExceptionInterface
     */
    private function createCircularReferenceException($id, array $path)
    {
        $id = (string) $id;
        return new \ECSPrefix20210509\Symfony\Contracts\Service\Anonymous__3e88683f5fba080472fe4fa460352f72__1(\sprintf('Circular reference detected for service "%s", path: "%s".', $id, \implode(' -> ', $path)));
    }
}
class Anonymous__3e88683f5fba080472fe4fa460352f72__0 extends \InvalidArgumentException implements \ECSPrefix20210509\Psr\Container\NotFoundExceptionInterface
{
}
class Anonymous__3e88683f5fba080472fe4fa460352f72__1 extends \RuntimeException implements \ECSPrefix20210509\Psr\Container\ContainerExceptionInterface
{
}
