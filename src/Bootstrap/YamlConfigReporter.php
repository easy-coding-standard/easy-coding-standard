<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Bootstrap;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\SmartFileSystem\SmartFileInfo;

final class YamlConfigReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct()
    {
        $this->symfonyStyle = (new SymfonyStyleFactory())->create();
    }

    /**
     * @param SmartFileInfo[] $configFileInfos
     */
    public function reportYamlConfig(array $configFileInfos): void
    {
        foreach ($configFileInfos as $configFileInfo) {
            if (! in_array($configFileInfo->getSuffix(), ['yml', 'yaml'], true)) {
                continue;
            }

            $warning = sprintf(
                'You are using YAML format in "%s" config.%sIt is deprecated and will be removed in next ECS 9. Switch to PHP format as soon as possible with "%s"',
                $configFileInfo->getRelativeFilePathFromCwd(),
                PHP_EOL,
                'https://github.com/migrify/config-transformer'
            );

            $this->symfonyStyle->warning($warning);

            sleep(3);
        }
    }
}
