<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\EasyTesting\FixtureSplitter;

use ECSPrefix20211002\Nette\Utils\Strings;
use ECSPrefix20211002\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent;
use ECSPrefix20211002\Symplify\EasyTesting\ValueObject\SplitLine;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20211002\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class TrioFixtureSplitter
{
    public function splitFileInfo(\ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \ECSPrefix20211002\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent
    {
        $parts = \ECSPrefix20211002\Nette\Utils\Strings::split($smartFileInfo->getContents(), \ECSPrefix20211002\Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
        $this->ensureHasThreeParts($parts, $smartFileInfo);
        return new \ECSPrefix20211002\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent($parts[0], $parts[1], $parts[2]);
    }
    /**
     * @param mixed[] $parts
     */
    private function ensureHasThreeParts(array $parts, \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : void
    {
        if (\count($parts) === 3) {
            return;
        }
        $message = \sprintf('The fixture "%s" should have 3 parts. %d found', $smartFileInfo->getRelativeFilePathFromCwd(), \count($parts));
        throw new \ECSPrefix20211002\Symplify\SymplifyKernel\Exception\ShouldNotHappenException($message);
    }
}
