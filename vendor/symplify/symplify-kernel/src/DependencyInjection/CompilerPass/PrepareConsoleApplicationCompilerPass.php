<?php

namespace Symplify\SymplifyKernel\DependencyInjection\CompilerPass;

use ECSPrefix20210509\Symfony\Component\Console\Application;
use ECSPrefix20210509\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210509\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210509\Symfony\Component\DependencyInjection\Reference;
use Symplify\SymplifyKernel\Console\AutowiredConsoleApplication;
use Symplify\SymplifyKernel\Console\ConsoleApplicationFactory;
final class PrepareConsoleApplicationCompilerPass implements \ECSPrefix20210509\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(\ECSPrefix20210509\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $consoleApplicationClass = $this->resolveConsoleApplicationClass($containerBuilder);
        if ($consoleApplicationClass === null) {
            $this->registerAutowiredSymfonyConsole($containerBuilder);
            return;
        }
        // add console application alias
        if ($consoleApplicationClass === \ECSPrefix20210509\Symfony\Component\Console\Application::class) {
            return;
        }
        $containerBuilder->setAlias(\ECSPrefix20210509\Symfony\Component\Console\Application::class, $consoleApplicationClass)->setPublic(\true);
        // calls
        // resolve name
        // resolve version
    }
    /**
     * @return string|null
     */
    private function resolveConsoleApplicationClass(\ECSPrefix20210509\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (!\is_a((string) $definition->getClass(), \ECSPrefix20210509\Symfony\Component\Console\Application::class, \true)) {
                continue;
            }
            return $definition->getClass();
        }
        return null;
    }
    /**
     * Missing console application? add basic one
     * @return void
     */
    private function registerAutowiredSymfonyConsole(\ECSPrefix20210509\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $containerBuilder->autowire(\Symplify\SymplifyKernel\Console\AutowiredConsoleApplication::class, \Symplify\SymplifyKernel\Console\AutowiredConsoleApplication::class)->setFactory([new \ECSPrefix20210509\Symfony\Component\DependencyInjection\Reference(\Symplify\SymplifyKernel\Console\ConsoleApplicationFactory::class), 'create']);
        $containerBuilder->setAlias(\ECSPrefix20210509\Symfony\Component\Console\Application::class, \Symplify\SymplifyKernel\Console\AutowiredConsoleApplication::class)->setPublic(\true);
    }
}
