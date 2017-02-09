<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symplify\EasyCodingStandard\Application\ApplicationRunner;
use Symplify\EasyCodingStandard\Contract\Application\ApplicationInterface;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class MultiCodingStandardExtension extends CompilerExtension
{
    public function loadConfiguration() : void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__.'/../config/services.neon')['services']
        );
    }

    public function beforeCompile() : void
    {
        $this->loadCommandsToConsoleApplication();
        $this->loadApplicationsToApplicationRunner();
    }

    private function loadCommandsToConsoleApplication() : void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            Application::class,
            Command::class,
            'add'
        );
    }

    private function loadApplicationsToApplicationRunner() : void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ApplicationRunner::class,
            ApplicationInterface::class,
            'addApplication'
        );
    }
}
