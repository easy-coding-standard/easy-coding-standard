<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Config;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use ECSPrefix202209\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202209\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use ECSPrefix202209\Webmozart\Assert\Assert;
use ECSPrefix202209\Webmozart\Assert\InvalidArgumentException;
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
        $parameters = $this->parameters();
        $parameters->set(Option::PATHS, $paths);
    }
    /**
     * @param mixed[] $skips
     */
    public function skip(array $skips) : void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::SKIP, $skips);
    }
    /**
     * @deprecated This feature is deprecated, as hard to configure right and confusing. Use skip() instead
     * @param mixed[] $onlys
     */
    public function only(array $onlys) : void
    {
        echo 'The option only() is deprecated as error prone and hard to do right. Use skip() with configuration instead.';
        \sleep(3);
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
        $this->isCheckerClass($checkerClass);
        $services = $this->services();
        $services->set($checkerClass)->public();
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
        $this->isCheckerClass($checkerClass);
        $services = $this->services();
        $service = $services->set($checkerClass);
        if (\is_a($checkerClass, FixerInterface::class, \true)) {
            Assert::isAnyOf($checkerClass, [ConfigurableFixerInterface::class, ConfigurableRuleInterface::class]);
            $service->call('configure', [$configuration]);
        }
        if (\is_a($checkerClass, Sniff::class, \true)) {
            foreach ($configuration as $propertyName => $value) {
                Assert::propertyExists($checkerClass, $propertyName);
                $service->property($propertyName, $value);
            }
        }
    }
    /**
     * @param Option::INDENTATION_* $indentation
     */
    public function indentation(string $indentation) : void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::INDENTATION, $indentation);
    }
    public function lineEnding(string $lineEnding) : void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::LINE_ENDING, $lineEnding);
    }
    public function cacheDirectory(string $cacheDirectory) : void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::CACHE_DIRECTORY, $cacheDirectory);
    }
    public function cacheNamespace(string $cacheNamespace) : void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::CACHE_NAMESPACE, $cacheNamespace);
    }
    /**
     * @param string[] $fileExtensions
     */
    public function fileExtensions(array $fileExtensions) : void
    {
        Assert::allString($fileExtensions);
        $parameters = $this->parameters();
        $parameters->set(Option::FILE_EXTENSIONS, $fileExtensions);
    }
    public function parallel(int $seconds = 120, int $maxNumberOfProcess = 16, int $jobSize = 20) : void
    {
        $parameters = $this->parameters();
        $parameters->set(Option::PARALLEL, \true);
        $parameters->set(Option::PARALLEL_TIMEOUT_IN_SECONDS, $seconds);
        $parameters->set(Option::PARALLEL_MAX_NUMBER_OF_PROCESSES, $maxNumberOfProcess);
        $parameters->set(Option::PARALLEL_JOB_SIZE, $jobSize);
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
        $parameters = $this->parameters();
        $parameters->set(Option::REPORT_SNIFF_WARNINGS, $sniffClasses);
    }
    /**
     * @param class-string $checkerClass
     */
    private function isCheckerClass(string $checkerClass) : void
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
