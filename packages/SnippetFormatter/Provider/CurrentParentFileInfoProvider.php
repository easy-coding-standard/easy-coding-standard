<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Provider;

use Symplify\SmartFileSystem\SmartFileInfo;

final class CurrentParentFileInfoProvider
{
    private ?SmartFileInfo $smartFileInfo = null;

    public function setParentFileInfo(SmartFileInfo $smartFileInfo): void
    {
        $this->smartFileInfo = $smartFileInfo;
    }

    public function provide(): ?SmartFileInfo
    {
        return $this->smartFileInfo;
    }
}
