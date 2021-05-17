<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210517\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use ECSPrefix20210517\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210517\Symfony\Component\HttpFoundation\Session\SessionInterface;
use ECSPrefix20210517\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use ECSPrefix20210517\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
/**
 * Yields the Session.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class SessionValueResolver implements \ECSPrefix20210517\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(\ECSPrefix20210517\Symfony\Component\HttpFoundation\Request $request, \ECSPrefix20210517\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument) : bool
    {
        if (!$request->hasSession()) {
            return \false;
        }
        $type = $argument->getType();
        if (\ECSPrefix20210517\Symfony\Component\HttpFoundation\Session\SessionInterface::class !== $type && !\is_subclass_of($type, \ECSPrefix20210517\Symfony\Component\HttpFoundation\Session\SessionInterface::class)) {
            return \false;
        }
        return $request->getSession() instanceof $type;
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function resolve(\ECSPrefix20210517\Symfony\Component\HttpFoundation\Request $request, \ECSPrefix20210517\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        (yield $request->getSession());
    }
}
