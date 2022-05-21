<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20220521\Symfony\Component\Config\Resource\FileResource;
use ECSPrefix20220521\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220521\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20220521\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20220521\Symfony\Component\DependencyInjection\ContainerInterface;
final class DeprecationReporter
{
    /**
     * @var array<string, string>
     */
    private const DEPRECATED_SETS_BY_FILE_PATHS = ['config/set/symfony.php' => 'SYMFONY', 'config/set/symfony-risky.php' => 'SYMFONY_RISKY', 'config/set/php-cs-fixer.php' => 'PHP_CS_FIXER', 'config/set/php-cs-fixer-risky.php' => 'PHP_CS_FIXER_RISKY'];
    public function reportDeprecatedSets(\ECSPrefix20220521\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, \ECSPrefix20220521\Symfony\Component\Console\Input\InputInterface $input) : void
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
    private function reportFoundSets(array $setNames, \ECSPrefix20220521\Symfony\Component\DependencyInjection\ContainerInterface $container) : void
    {
        $symfonyStyle = $container->get(\ECSPrefix20220521\Symfony\Component\Console\Style\SymfonyStyle::class);
        foreach ($setNames as $setName) {
            $deprecatedMessage = \sprintf('The "%s" set from ECS is outdated and deprecated. Switch to standardized "PSR_12" or include rules manually.', $setName);
            $symfonyStyle->warning($deprecatedMessage);
        }
        // to make deprecation noticeable
        \sleep(3);
    }
    /**
     * @return string[]
     */
    private function findDeprecatedSets(\ECSPrefix20220521\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : array
    {
        $deprecatedSets = [];
        foreach ($containerBuilder->getResources() as $resource) {
            if (!$resource instanceof \ECSPrefix20220521\Symfony\Component\Config\Resource\FileResource) {
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
