<?php

namespace Symplify\SetConfigResolver\Config;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\SetConfigResolver\SetResolver;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SetsParameterResolver
{
    /**
     * @var string
     */
    const SETS = 'sets';

    /**
     * @var SetResolver
     */
    private $setResolver;

    public function __construct(SetResolver $setResolver)
    {
        $this->setResolver = $setResolver;
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return mixed[]
     */
    public function resolveFromFileInfos(array $fileInfos)
    {
        $setFileInfos = [];
        foreach ($fileInfos as $fileInfo) {
            $setsNames = $this->resolveSetsFromFileInfo($fileInfo);
            foreach ($setsNames as $setsName) {
                $setFileInfos[] = $this->setResolver->detectFromName($setsName);
            }
        }

        return $setFileInfos;
    }

    /**
     * @return mixed[]
     */
    private function resolveSetsFromFileInfo(SmartFileInfo $configFileInfo)
    {
        if ($configFileInfo->hasSuffixes(['yml', 'yaml'])) {
            throw new ShouldNotHappenException(
                'Only PHP config suffix is supported now. Migrete your Symfony config to PHP'
            );
        }

        return $this->resolveSetsParameterFromPhpFileInfo($configFileInfo);
    }

    /**
     * @return mixed[]
     */
    private function resolveSetsParameterFromPhpFileInfo(SmartFileInfo $configFileInfo)
    {
        // php file loader
        $containerBuilder = new ContainerBuilder();
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator());
        $phpFileLoader->load($configFileInfo->getRealPath());

        if (! $containerBuilder->hasParameter(self::SETS)) {
            return [];
        }

        return (array) $containerBuilder->getParameter(self::SETS);
    }
}
