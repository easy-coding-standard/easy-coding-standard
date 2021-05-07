<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Contracts\Service;

use ECSPrefix20210507\Psr\Container\ContainerInterface;
/**
 * Implementation of ServiceSubscriberInterface that determines subscribed services from
 * private method return types. Service ids are available as "ClassName::methodName".
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait ServiceSubscriberTrait
{
    /** @var ContainerInterface */
    protected $container;
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public static function getSubscribedServices()
    {
        static $services;
        if (null !== $services) {
            return $services;
        }
        $services = \is_callable(['parent', __FUNCTION__]) ? parent::getSubscribedServices() : [];
        foreach ((new \ReflectionClass(self::class))->getMethods() as $method) {
            if ($method->isStatic() || $method->isAbstract() || $method->isGenerator() || $method->isInternal() || $method->getNumberOfRequiredParameters()) {
                continue;
            }
            if (self::class === $method->getDeclaringClass()->name && ($returnType = $method->getReturnType()) && !$returnType->isBuiltin()) {
                $services[self::class . '::' . $method->name] = '?' . ($returnType instanceof \ReflectionNamedType ? $returnType->getName() : $returnType);
            }
        }
        return $services;
    }
    /**
     * @required
     * @param \ECSPrefix20210507\Psr\Container\ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
        if (\is_callable(['parent', __FUNCTION__])) {
            return parent::setContainer($container);
        }
        return null;
    }
}
