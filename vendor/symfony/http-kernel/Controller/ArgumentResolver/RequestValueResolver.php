<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
/**
 * Yields the same instance as the request object passed along.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class RequestValueResolver implements \ConfigTransformer20210601\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(\ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request $request, \ConfigTransformer20210601\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument) : bool
    {
        return \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request::class === $argument->getType() || \is_subclass_of($argument->getType(), \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request::class);
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function resolve(\ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request $request, \ConfigTransformer20210601\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        (yield $request);
    }
}
