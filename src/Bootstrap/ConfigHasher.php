<?php

namespace Symplify\EasyCodingStandard\Bootstrap;

use Symplify\SmartFileSystem\SmartFileInfo;
final class ConfigHasher
{
    /**
     * @api
     * @param SmartFileInfo[] $configFileInfos
     * @return string
     */
    public function computeFileInfosHash(array $configFileInfos)
    {
        $hash = '';
        foreach ($configFileInfos as $config) {
            $hash .= \md5_file($config->getRealPath());
        }
        return $hash;
    }
}
