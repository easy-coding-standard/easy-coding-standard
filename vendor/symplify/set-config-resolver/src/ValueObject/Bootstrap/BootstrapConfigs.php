<?php

namespace Symplify\SetConfigResolver\ValueObject\Bootstrap;

use Symplify\SmartFileSystem\SmartFileInfo;
final class BootstrapConfigs
{
    /**
     * @var SmartFileInfo|null
     */
    private $mainConfigFileInfo;
    /**
     * @var SmartFileInfo[]
     */
    private $setConfigFileInfos = [];
    /**
     * @param SmartFileInfo[] $setConfigFileInfos
     * @param \Symplify\SmartFileSystem\SmartFileInfo|null $mainConfigFileInfo
     */
    public function __construct($mainConfigFileInfo, array $setConfigFileInfos)
    {
        $this->mainConfigFileInfo = $mainConfigFileInfo;
        $this->setConfigFileInfos = $setConfigFileInfos;
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    public function getMainConfigFileInfo()
    {
        return $this->mainConfigFileInfo;
    }
    /**
     * @return mixed[]
     */
    public function getConfigFileInfos()
    {
        if (!$this->mainConfigFileInfo instanceof \Symplify\SmartFileSystem\SmartFileInfo) {
            return $this->setConfigFileInfos;
        }
        return \array_merge($this->setConfigFileInfos, [$this->mainConfigFileInfo]);
    }
}
