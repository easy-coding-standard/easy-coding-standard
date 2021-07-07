<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210707\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use ECSPrefix20210707\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210707\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use ECSPrefix20210707\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
/**
 * Yields a non-variadic argument's value from the request attributes.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class RequestAttributeValueResolver implements \ECSPrefix20210707\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(\ECSPrefix20210707\Symfony\Component\HttpFoundation\Request $request, \ECSPrefix20210707\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument) : bool
    {
        return !$argument->isVariadic() && $request->attributes->has($argument->getName());
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function resolve(\ECSPrefix20210707\Symfony\Component\HttpFoundation\Request $request, \ECSPrefix20210707\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        (yield $request->attributes->get($argument->getName()));
    }
}
