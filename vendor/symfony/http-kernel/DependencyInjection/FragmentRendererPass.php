<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210514\Symfony\Component\HttpKernel\DependencyInjection;

use ECSPrefix20210514\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ECSPrefix20210514\Symfony\Component\DependencyInjection\Reference;
use ECSPrefix20210514\Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
/**
 * Adds services tagged kernel.fragment_renderer as HTTP content rendering strategies.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FragmentRendererPass implements \ECSPrefix20210514\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private $handlerService;
    private $rendererTag;
    /**
     * @param string $handlerService
     * @param string $rendererTag
     */
    public function __construct($handlerService = 'fragment.handler', $rendererTag = 'kernel.fragment_renderer')
    {
        $handlerService = (string) $handlerService;
        $rendererTag = (string) $rendererTag;
        $this->handlerService = $handlerService;
        $this->rendererTag = $rendererTag;
    }
    public function process(\ECSPrefix20210514\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->handlerService)) {
            return;
        }
        $definition = $container->getDefinition($this->handlerService);
        $renderers = [];
        foreach ($container->findTaggedServiceIds($this->rendererTag, \true) as $id => $tags) {
            $def = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            if (!($r = $container->getReflectionClass($class))) {
                throw new \ECSPrefix20210514\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }
            if (!$r->isSubclassOf(\ECSPrefix20210514\Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface::class)) {
                throw new \ECSPrefix20210514\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Service "%s" must implement interface "%s".', $id, \ECSPrefix20210514\Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface::class));
            }
            foreach ($tags as $tag) {
                $renderers[$tag['alias']] = new \ECSPrefix20210514\Symfony\Component\DependencyInjection\Reference($id);
            }
        }
        $definition->replaceArgument(0, \ECSPrefix20210514\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass::register($container, $renderers));
    }
}
