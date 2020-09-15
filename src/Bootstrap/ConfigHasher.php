<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Bootstrap;

use Symplify\SmartFileSystem\SmartFileInfo;

final class ConfigHasher
{
    /**
     * @param SmartFileInfo[] $configFileInfos
     */
    public function computeFileInfosHash(array $configFileInfos): string
    {
        $hash = '';
        foreach ($configFileInfos as $config) {
            $hash .= md5_file($config->getRealPath());
        }

        return $hash;
    }
}
