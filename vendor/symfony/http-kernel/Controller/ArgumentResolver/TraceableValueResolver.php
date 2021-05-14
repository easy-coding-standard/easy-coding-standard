<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210514\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use ECSPrefix20210514\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210514\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use ECSPrefix20210514\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use ECSPrefix20210514\Symfony\Component\Stopwatch\Stopwatch;
/**
 * Provides timing information via the stopwatch.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class TraceableValueResolver implements \ECSPrefix20210514\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
{
    private $inner;
    private $stopwatch;
    public function __construct(\ECSPrefix20210514\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface $inner, \ECSPrefix20210514\Symfony\Component\Stopwatch\Stopwatch $stopwatch)
    {
        $this->inner = $inner;
        $this->stopwatch = $stopwatch;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function supports(\ECSPrefix20210514\Symfony\Component\HttpFoundation\Request $request, \ECSPrefix20210514\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        $method = \get_class($this->inner) . '::' . __FUNCTION__;
        $this->stopwatch->start($method, 'controller.argument_value_resolver');
        $return = $this->inner->supports($request, $argument);
        $this->stopwatch->stop($method);
        return $return;
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function resolve(\ECSPrefix20210514\Symfony\Component\HttpFoundation\Request $request, \ECSPrefix20210514\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        $method = \get_class($this->inner) . '::' . __FUNCTION__;
        $this->stopwatch->start($method, 'controller.argument_value_resolver');
        yield from $this->inner->resolve($request, $argument);
        $this->stopwatch->stop($method);
    }
}
