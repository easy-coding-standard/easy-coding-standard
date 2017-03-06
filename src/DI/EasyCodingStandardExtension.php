<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class EasyCodingStandardExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    public function beforeCompile(): void
    {
        $this->loadCommandsToConsoleApplication();
        $this->loadFileProcessorsToApplication();
    }

    private function loadCommandsToConsoleApplication(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            ConsoleApplication::class,
            Command::class,
            'add'
        );
    }

    private function loadFileProcessorsToApplication(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            Application::class,
            FileProcessorInterface::class,
            'addFileProcessor'
        );
    }
}
