<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Nette\Utils\Json;
use PHP_CodeSniffer\Config as SnifferConfig;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\DependencyInjection\LazyContainerFactory;
use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException;

/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\FileHashComputer\FileHashComputerTest
 */
final class FileHashComputer
{
    public function computeConfig(string $filePath): string
    {
        $ecsConfig = (new LazyContainerFactory())->create([$filePath]);

        $services = array_reduce(
            array_keys($ecsConfig->getBindings()),
            function ($services, $className) use ($ecsConfig): array {
                $service = $ecsConfig->get($className);

                $isSniff = $service instanceof Sniff;
                $isFixer = $service instanceof FixerInterface;

                return [
                    $className => match (true) {
                        $isSniff => $this->getSniffConfiguration($service),
                        $isFixer => $this->getFixerConfiguration($service),

                        // It's too hard to define a good general rule for serialization.
                        // An arbitrary class property could include a timestamp somewhere,
                        // causing permanent cache invalidation, for instance.
                        default => [],
                    },
                    ...$services,
                ];
            },
            []
        );

        $servicesHash = sha1(Json::encode($services));
        $snifferConfigHash = sha1(Json::encode($ecsConfig->get(SnifferConfig::class)->getSettings()));

        return sha1(implode('', [
            $servicesHash,
            $snifferConfigHash,
            SimpleParameterProvider::hash(),
            StaticVersionResolver::resolvePackageVersion(),
        ]));
    }

    public function compute(string $filePath): string
    {
        $fileHash = md5_file($filePath);
        if (! $fileHash) {
            throw new FileNotFoundException(sprintf('File "%s" was not found', $fileHash));
        }

        return $fileHash;
    }

    /**
     * All configurable options must be public properties.
     *
     * @return array<string, mixed>
     *
     * @see https://github.com/squizlabs/PHP_CodeSniffer/blob/c6c65ca0dc8608ba87631523b97b2f8d5351a854/src/Ruleset.php#L1309
     */
    private function getSniffConfiguration(Sniff $sniff): array
    {
        return get_object_vars($sniff);
    }

    /**
     * Unlike for sniffers, fixers don't have any requirements on their
     * configuration being accessible.
     *
     * Previously `AbstractFixer` provided a configuration property, but it was
     * removed in 5.39.3, replaced with a definition in
     * `ConfigurableFixerTrait`. But since this is marked `@internal` and a
     * breaking change to the interface occurred on a patch release, we
     * definitely don't want to rely on it.
     *
     * So we have to be overly cautious and just return EVERY property.
     *
     * @return array<string, mixed>
     */
    private function getFixerConfiguration(FixerInterface $fixer): array
    {
        if (! $fixer instanceof ConfigurableFixerInterface) {
            return [];
        }

        /// This will return all non-static instance properties.
        /// @see https://www.php.net/manual/en/function.get-object-vars.php#47075
        return (array) $fixer;
    }
}
