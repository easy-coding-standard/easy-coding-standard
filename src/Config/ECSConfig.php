<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Config;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202306\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use ECSPrefix202306\Webmozart\Assert\Assert;
use ECSPrefix202306\Webmozart\Assert\InvalidArgumentException;
/**
 * @api
 */
final class ECSConfig extends ContainerConfigurator
{
    /**
     * @param string[] $paths
     */
    public function paths(array $paths) : void
    {
        Assert::allString($paths);
        SimpleParameterProvider::setParameter(Option::PATHS, $paths);
    }
    /**
     * @param list<string>|array<class-string<Sniff|FixerInterface>, list<string>|null> $skips
     */
    public function skip(array $skips) : void
    {
        SimpleParameterProvider::addParameter(Option::SKIP, $skips);
    }
    /**
     * @param string[] $sets
     */
    public function sets(array $sets) : void
    {
        Assert::allString($sets);
        Assert::allFileExists($sets);
        foreach ($sets as $set) {
            $this->import($set);
        }
    }
    /**
     * @param class-string<Sniff|FixerInterface> $checkerClass
     */
    public function rule(string $checkerClass) : void
    {
        $this->assertCheckerClass($checkerClass);
        // tag for autowiring of tagged_iterator()
        $interfaceTag = \is_a($checkerClass, Sniff::class, \true) ? Sniff::class : FixerInterface::class;
        $servicesConfigurator = $this->services();
        $servicesConfigurator->set($checkerClass)->public()->autowire()->tag($interfaceTag);
    }
    /**
     * @param array<class-string<Sniff|FixerInterface>> $checkerClasses
     */
    public function rules(array $checkerClasses) : void
    {
        $this->ensureCheckerClassesAreUnique($checkerClasses);
        foreach ($checkerClasses as $checkerClass) {
            $this->rule($checkerClass);
        }
    }
    /**
     * @param class-string $checkerClass
     * @param mixed[] $configuration
     */
    public function ruleWithConfiguration(string $checkerClass, array $configuration) : void
    {
        $this->assertCheckerClass($checkerClass);
        $services = $this->services();
        $serviceConfigurator = $services->set($checkerClass)->autowire();
        // tag for autowiring of tagged_iterator()
        $interfaceTag = \is_a($checkerClass, Sniff::class, \true) ? Sniff::class : FixerInterface::class;
        $serviceConfigurator->tag($interfaceTag);
        if (\is_a($checkerClass, FixerInterface::class, \true)) {
            Assert::isAnyOf($checkerClass, [ConfigurableFixerInterface::class, ConfigurableRuleInterface::class]);
            $serviceConfigurator->call('configure', [$configuration]);
        }
        if (\is_a($checkerClass, Sniff::class, \true)) {
            foreach ($configuration as $propertyName => $value) {
                Assert::propertyExists($checkerClass, $propertyName);
                $serviceConfigurator->property($propertyName, $value);
            }
        }
    }
    /**
     * @param array<class-string<Sniff|FixerInterface>, mixed[]> $rulesWithConfiguration
     */
    public function rulesWithConfiguration(array $rulesWithConfiguration) : void
    {
        Assert::allIsArray($rulesWithConfiguration);
        foreach ($rulesWithConfiguration as $checkerClass => $configuration) {
            $this->ruleWithConfiguration($checkerClass, $configuration);
        }
    }
    /**
     * @param Option::INDENTATION_* $indentation
     */
    public function indentation(string $indentation) : void
    {
        SimpleParameterProvider::setParameter(Option::INDENTATION, $indentation);
    }
    public function lineEnding(string $lineEnding) : void
    {
        SimpleParameterProvider::setParameter(Option::LINE_ENDING, $lineEnding);
    }
    public function cacheDirectory(string $cacheDirectory) : void
    {
        SimpleParameterProvider::setParameter(Option::CACHE_DIRECTORY, $cacheDirectory);
    }
    public function cacheNamespace(string $cacheNamespace) : void
    {
        SimpleParameterProvider::setParameter(Option::CACHE_NAMESPACE, $cacheNamespace);
    }
    /**
     * @param string[] $fileExtensions
     */
    public function fileExtensions(array $fileExtensions) : void
    {
        Assert::allString($fileExtensions);
        SimpleParameterProvider::addParameter(Option::FILE_EXTENSIONS, $fileExtensions);
    }
    public function parallel(int $seconds = 120, int $maxNumberOfProcess = 16, int $jobSize = 20) : void
    {
        SimpleParameterProvider::setParameter(Option::PARALLEL, \true);
        SimpleParameterProvider::setParameter(Option::PARALLEL_TIMEOUT_IN_SECONDS, $seconds);
        SimpleParameterProvider::setParameter(Option::PARALLEL_MAX_NUMBER_OF_PROCESSES, $maxNumberOfProcess);
        SimpleParameterProvider::setParameter(Option::PARALLEL_JOB_SIZE, $jobSize);
    }
    /**
     * @api
     */
    public function disableParallel() : void
    {
        SimpleParameterProvider::setParameter(Option::PARALLEL, \false);
    }
    /**
     * @param array<class-string<Sniff>> $sniffClasses
     */
    public function reportSniffClassWarnings(array $sniffClasses) : void
    {
        foreach ($sniffClasses as $sniffClass) {
            Assert::classExists($sniffClass);
            Assert::isAnyOf($sniffClass, [Sniff::class]);
        }
        SimpleParameterProvider::addParameter(Option::REPORT_SNIFF_WARNINGS, $sniffClasses);
    }
    /**
     * @link https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/ruleSets/index.rst
     * @param list<string> $setNames
     */
    public function dynamicSets(array $setNames) : void
    {
        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();
        $ruleSet = new RuleSet(\array_fill_keys($setNames, \true));
        $fixerFactory->useRuleSet($ruleSet);
        /** @var FixerInterface $fixer */
        foreach ($fixerFactory->getFixers() as $fixer) {
            $ruleConfiguration = $ruleSet->getRuleConfiguration($fixer->getName());
            if ($ruleConfiguration === null) {
                $this->rule(\get_class($fixer));
            } else {
                $this->ruleWithConfiguration(\get_class($fixer), $ruleConfiguration);
            }
        }
    }
    /**
     * @param class-string $checkerClass
     */
    private function assertCheckerClass(string $checkerClass) : void
    {
        Assert::classExists($checkerClass);
        Assert::isAnyOf($checkerClass, [Sniff::class, FixerInterface::class]);
    }
    /**
     * @param string[] $checkerClasses
     */
    private function ensureCheckerClassesAreUnique(array $checkerClasses) : void
    {
        // ensure all rules are registered exactly once
        $checkerClassToCount = \array_count_values($checkerClasses);
        $duplicatedCheckerClassToCount = \array_filter($checkerClassToCount, static function (int $count) : bool {
            return $count > 1;
        });
        if ($duplicatedCheckerClassToCount === []) {
            return;
        }
        $duplicatedCheckerClasses = \array_flip($duplicatedCheckerClassToCount);
        $errorMessage = \sprintf('There are duplicated classes in $rectorConfig->rules(): "%s". Make them unique to avoid unexpected behavior.', \implode('", "', $duplicatedCheckerClasses));
        throw new InvalidArgumentException($errorMessage);
    }
}
