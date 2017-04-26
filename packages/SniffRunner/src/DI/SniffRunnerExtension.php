<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Configuration\Option\SniffsOption;
use Symplify\EasyCodingStandard\SniffRunner\Contract\SniffCollectorInterface;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class SniffRunnerExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );

        $sniffs = $this->getContainerBuilder()->parameters[SniffsOption::NAME];
        $this->registerSniffsAsServices($sniffs);
    }

    public function beforeCompile(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            SniffCollectorInterface::class,
            Sniff::class,
            'addSniff'
        );
    }

    /**
     * @param mixed[] $sniffs
     */
    private function registerSniffsAsServices(array $sniffs): void
    {
        $containerBuilder = $this->getContainerBuilder();
        foreach ($sniffs as $sniffClass => $configuration) {
            $sniffDefinition = $this->createSniffDefinition($sniffClass, $configuration);
            $containerBuilder->addDefinition(md5($sniffClass), $sniffDefinition);
        }
    }

    /**
     * @param string $sniffClass
     * @param mixed[] $configuration
     */
    private function createSniffDefinition(string $sniffClass, array $configuration): ServiceDefinition
    {
        $sniffDefinition = new ServiceDefinition();
        $sniffDefinition->setClass($sniffClass);

        foreach ($configuration as $property => $value) {
            $sniffDefinition->addSetup(
                '$' . $property,
                [$this->escapeValue($value)]
            );
        }

        return $sniffDefinition;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function escapeValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $subValue) {
                if (is_string($subValue)) {
                    $value[$key] = $this->escapeAtSign($subValue);
                }
            }
        }

        if (is_string($value)) {
            return $this->escapeAtSign($value);
        }

        return $value;
    }

    private function escapeAtSign(string $value): string
    {
        if (Strings::startsWith($value, '@')) {
            return '@' . $value;
        }

        return $value;
    }
}
