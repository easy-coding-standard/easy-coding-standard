<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix202206\Symfony\Component\Config\Resource\FileResource;
use ECSPrefix202206\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202206\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix202206\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix202206\Symfony\Component\DependencyInjection\ContainerInterface;
final class DeprecationReporter
{
    /**
     * @var array<string, string>
     */
    private const DEPRECATED_SETS_BY_FILE_PATHS = ['config/set/symfony.php' => 'SYMFONY', 'config/set/symfony-risky.php' => 'SYMFONY_RISKY', 'config/set/php-cs-fixer.php' => 'PHP_CS_FIXER', 'config/set/php-cs-fixer-risky.php' => 'PHP_CS_FIXER_RISKY'];
    public function reportDeprecatedSets(ContainerBuilder $containerBuilder, InputInterface $input) : void
    {
        // report only once on main command run, not on parallel workers
        if ($input->getFirstArgument() !== 'check') {
            return;
        }
        $foundDeprecatedSets = $this->findDeprecatedSets($containerBuilder);
        if ($foundDeprecatedSets === []) {
            return;
        }
        $this->reportFoundSets($foundDeprecatedSets, $containerBuilder);
    }
    /**
     * @param string[] $setNames
     */
    private function reportFoundSets(array $setNames, ContainerInterface $container) : void
    {
        $symfonyStyle = $container->get(SymfonyStyle::class);
        foreach ($setNames as $setName) {
            $deprecatedMessage = \sprintf('The "%s" set from ECS is outdated and deprecated. Switch to standardized "PSR_12" or include rules manually.', $setName);
            $symfonyStyle->warning($deprecatedMessage);
        }
        // to make deprecation noticeable
        \sleep(10);
    }
    /**
     * @return string[]
     */
    private function findDeprecatedSets(ContainerBuilder $containerBuilder) : array
    {
        $deprecatedSets = [];
        foreach ($containerBuilder->getResources() as $resource) {
            if (!$resource instanceof FileResource) {
                continue;
            }
            foreach (self::DEPRECATED_SETS_BY_FILE_PATHS as $setFilePath => $setName) {
                if (\substr_compare($resource->getResource(), $setFilePath, -\strlen($setFilePath)) !== 0) {
                    continue;
                }
                $deprecatedSets[] = $setName;
            }
        }
        return $deprecatedSets;
    }
}
