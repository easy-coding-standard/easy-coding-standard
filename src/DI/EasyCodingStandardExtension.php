<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
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
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            Application::class,
            FileProcessorInterface::class,
            'addFileProcessor'
        );
    }
}
