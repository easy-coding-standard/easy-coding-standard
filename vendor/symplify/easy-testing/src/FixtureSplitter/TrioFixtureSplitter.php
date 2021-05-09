<?php

namespace Symplify\EasyTesting\FixtureSplitter;

use ECSPrefix20210509\Nette\Utils\Strings;
use Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent;
use Symplify\EasyTesting\ValueObject\SplitLine;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class TrioFixtureSplitter
{
    /**
     * @return \Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent
     */
    public function splitFileInfo(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        $parts = \ECSPrefix20210509\Nette\Utils\Strings::split($smartFileInfo->getContents(), \Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
        $this->ensureHasThreeParts($parts, $smartFileInfo);
        return new \Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent($parts[0], $parts[1], $parts[2]);
    }
    /**
     * @param mixed[] $parts
     * @return void
     */
    private function ensureHasThreeParts(array $parts, \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        if (\count($parts) === 3) {
            return;
        }
        $message = \sprintf('The fixture "%s" should have 3 parts. %d found', $smartFileInfo->getRelativeFilePathFromCwd(), \count($parts));
        throw new \Symplify\SymplifyKernel\Exception\ShouldNotHappenException($message);
    }
}
