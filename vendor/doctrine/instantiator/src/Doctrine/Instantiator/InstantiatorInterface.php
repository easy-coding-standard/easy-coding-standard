<?php

namespace ECSPrefix20210803\Doctrine\Instantiator;

use ECSPrefix20210803\Doctrine\Instantiator\Exception\ExceptionInterface;
/**
 * Instantiator provides utility methods to build objects without invoking their constructors
 */
interface InstantiatorInterface
{
    /**
     * @param string $className
     *
     * @return object
     *
     * @throws ExceptionInterface
     *
     * @template T of object
     * @phpstan-param class-string<T> $className
     */
    public function instantiate($className);
}
