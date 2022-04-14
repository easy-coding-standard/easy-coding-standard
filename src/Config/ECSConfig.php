<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Config;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20220414\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use ECSPrefix20220414\Webmozart\Assert\Assert;
/**
 * @api
 */
final class ECSConfig extends \Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function paths(array $paths) : void
    {
        \ECSPrefix20220414\Webmozart\Assert\Assert::allString($paths);
        $parameters = $this->parameters();
        $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS, $paths);
    }
    /**
     * @param mixed[] $skips
     */
    public function skip(array $skips) : void
    {
        $parameters = $this->parameters();
        $parameters->set(\Symplify\EasyCodingStandard\ValueObject\Option::SKIP, $skips);
    }
    /**
     * @param string[] $sets
     */
    public function sets(array $sets) : void
    {
        \ECSPrefix20220414\Webmozart\Assert\Assert::allString($sets);
        \ECSPrefix20220414\Webmozart\Assert\Assert::allFileExists($sets);
        foreach ($sets as $set) {
            $this->import($set);
        }
    }
    /**
     * @param class-string<Sniff|FixerInterface> $checkerClass
     */
    public function rule(string $checkerClass) : void
    {
        $this->isCheckerClass($checkerClass);
        $services = $this->services();
        $services->set($checkerClass);
    }
    /**
     * @param class-string $checkerClass
     * @param mixed[] $configuration
     */
    public function ruleWithConfiguration(string $checkerClass, array $configuration) : void
    {
        $this->isCheckerClass($checkerClass);
        $services = $this->services();
        $service = $services->set($checkerClass);
        if (\is_a($checkerClass, \PhpCsFixer\Fixer\FixerInterface::class, \true)) {
            \ECSPrefix20220414\Webmozart\Assert\Assert::isAnyOf($checkerClass, [\PhpCsFixer\Fixer\ConfigurableFixerInterface::class, \ECSPrefix20220414\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface::class]);
            $service->call('configure', [$configuration]);
        }
        // @todo
    }
    /**
     * @param class-string $checkerClass
     */
    private function isCheckerClass(string $checkerClass) : void
    {
        \ECSPrefix20220414\Webmozart\Assert\Assert::isAnyOf($checkerClass, [\PHP_CodeSniffer\Sniffs\Sniff::class, \PhpCsFixer\Fixer\FixerInterface::class]);
    }
}
