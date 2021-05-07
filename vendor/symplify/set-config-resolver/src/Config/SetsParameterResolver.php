<?php

namespace Symplify\SetConfigResolver\Config;

use ECSPrefix20210507\Symfony\Component\Config\FileLocator;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
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
    /**
     * @param \Symplify\SetConfigResolver\SetResolver $setResolver
     */
    public function __construct($setResolver)
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
     * @param \Symplify\SmartFileSystem\SmartFileInfo $configFileInfo
     */
    private function resolveSetsFromFileInfo($configFileInfo)
    {
        if ($configFileInfo->hasSuffixes(['yml', 'yaml'])) {
            throw new \Symplify\Astral\Exception\ShouldNotHappenException('Only PHP config suffix is supported now. Migrete your Symfony config to PHP');
        }
        return $this->resolveSetsParameterFromPhpFileInfo($configFileInfo);
    }
    /**
     * @return mixed[]
     * @param \Symplify\SmartFileSystem\SmartFileInfo $configFileInfo
     */
    private function resolveSetsParameterFromPhpFileInfo($configFileInfo)
    {
        // php file loader
        $containerBuilder = new \ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder();
        $phpFileLoader = new \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20210507\Symfony\Component\Config\FileLocator());
        $phpFileLoader->load($configFileInfo->getRealPath());
        if (!$containerBuilder->hasParameter(self::SETS)) {
            return [];
        }
        return (array) $containerBuilder->getParameter(self::SETS);
    }
}
