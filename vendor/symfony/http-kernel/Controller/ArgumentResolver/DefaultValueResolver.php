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
 * Yields the default value defined in the action signature when no value has been given.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class DefaultValueResolver implements \ConfigTransformer20210601\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(\ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request $request, \ConfigTransformer20210601\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument) : bool
    {
        return $argument->hasDefaultValue() || null !== $argument->getType() && $argument->isNullable() && !$argument->isVariadic();
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function resolve(\ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request $request, \ConfigTransformer20210601\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        (yield $argument->hasDefaultValue() ? $argument->getDefaultValue() : null);
    }
}
