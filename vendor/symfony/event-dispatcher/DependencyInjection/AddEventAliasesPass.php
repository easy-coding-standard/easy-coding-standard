<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210511\Symfony\Component\EventDispatcher\DependencyInjection;

use ECSPrefix20210511\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210511\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * This pass allows bundles to extend the list of event aliases.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 */
class AddEventAliasesPass implements \ECSPrefix20210511\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private $eventAliases;
    private $eventAliasesParameter;
    /**
     * @param string $eventAliasesParameter
     */
    public function __construct(array $eventAliases, $eventAliasesParameter = 'event_dispatcher.event_aliases')
    {
        $eventAliasesParameter = (string) $eventAliasesParameter;
        $this->eventAliases = $eventAliases;
        $this->eventAliasesParameter = $eventAliasesParameter;
    }
    /**
     * @return void
     */
    public function process(\ECSPrefix20210511\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $eventAliases = $container->hasParameter($this->eventAliasesParameter) ? $container->getParameter($this->eventAliasesParameter) : [];
        $container->setParameter($this->eventAliasesParameter, \array_merge($eventAliases, $this->eventAliases));
    }
}
