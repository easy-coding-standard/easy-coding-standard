<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210512\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use ECSPrefix20210512\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210512\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use ECSPrefix20210512\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
/**
 * Yields a variadic argument's values from the request attributes.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class VariadicValueResolver implements \ECSPrefix20210512\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function supports(\ECSPrefix20210512\Symfony\Component\HttpFoundation\Request $request, \ECSPrefix20210512\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        return $argument->isVariadic() && $request->attributes->has($argument->getName());
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function resolve(\ECSPrefix20210512\Symfony\Component\HttpFoundation\Request $request, \ECSPrefix20210512\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        $values = $request->attributes->get($argument->getName());
        if (!\is_array($values)) {
            throw new \InvalidArgumentException(\sprintf('The action argument "...$%1$s" is required to be an array, the request attribute "%1$s" contains a type of "%2$s" instead.', $argument->getName(), \get_debug_type($values)));
        }
        yield from $values;
    }
}
