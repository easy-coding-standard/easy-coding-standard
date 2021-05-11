<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210511\Symfony\Component\HttpKernel\DependencyInjection;

use ECSPrefix20210511\Psr\Container\ContainerInterface;
use ECSPrefix20210511\Symfony\Component\HttpFoundation\RequestStack;
use ECSPrefix20210511\Symfony\Component\HttpKernel\Fragment\FragmentHandler;
/**
 * Lazily loads fragment renderers from the dependency injection container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LazyLoadingFragmentHandler extends \ECSPrefix20210511\Symfony\Component\HttpKernel\Fragment\FragmentHandler
{
    private $container;
    private $initialized = [];
    /**
     * @param bool $debug
     */
    public function __construct(\ECSPrefix20210511\Psr\Container\ContainerInterface $container, \ECSPrefix20210511\Symfony\Component\HttpFoundation\RequestStack $requestStack, $debug = \false)
    {
        $debug = (bool) $debug;
        $this->container = $container;
        parent::__construct($requestStack, [], $debug);
    }
    /**
     * {@inheritdoc}
     * @param string $renderer
     */
    public function render($uri, $renderer = 'inline', array $options = [])
    {
        $renderer = (string) $renderer;
        if (!isset($this->initialized[$renderer]) && $this->container->has($renderer)) {
            $this->addRenderer($this->container->get($renderer));
            $this->initialized[$renderer] = \true;
        }
        return parent::render($uri, $renderer, $options);
    }
}
