<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Provider;

use SplFileInfo;
final class CurrentParentFileInfoProvider
{
    /**
     * @var \SplFileInfo|null
     */
    private $fileInfo;
    public function setParentFileInfo(SplFileInfo $fileInfo) : void
    {
        $this->fileInfo = $fileInfo;
    }
    public function provide() : ?SplFileInfo
    {
        return $this->fileInfo;
    }
}
