<?php

namespace ECSPrefix20210515\Symplify\EasyTesting\FixtureSplitter;

use ECSPrefix20210515\Nette\Utils\Strings;
use ECSPrefix20210515\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent;
use ECSPrefix20210515\Symplify\EasyTesting\ValueObject\SplitLine;
use ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210515\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class TrioFixtureSplitter
{
    /**
     * @return \Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent
     */
    public function splitFileInfo(\ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $parts = \ECSPrefix20210515\Nette\Utils\Strings::split($smartFileInfo->getContents(), \ECSPrefix20210515\Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
        $this->ensureHasThreeParts($parts, $smartFileInfo);
        return new \ECSPrefix20210515\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent($parts[0], $parts[1], $parts[2]);
    }
    /**
     * @param mixed[] $parts
     * @return void
     */
    private function ensureHasThreeParts(array $parts, \ECSPrefix20210515\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        if (\count($parts) === 3) {
            return;
        }
        $message = \sprintf('The fixture "%s" should have 3 parts. %d found', $smartFileInfo->getRelativeFilePathFromCwd(), \count($parts));
        throw new \ECSPrefix20210515\Symplify\SymplifyKernel\Exception\ShouldNotHappenException($message);
    }
}
