<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Provider;

use SplFileInfo;

final class CurrentParentFileInfoProvider
{
    private ?SplFileInfo $fileInfo = null;

    public function setParentFileInfo(SplFileInfo $fileInfo): void
    {
        $this->fileInfo = $fileInfo;
    }

    public function provide(): ?SplFileInfo
    {
        return $this->fileInfo;
    }
}
