<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\EasyTesting\FixtureSplitter;

use ECSPrefix20220220\Nette\Utils\Strings;
use ECSPrefix20220220\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent;
use ECSPrefix20220220\Symplify\EasyTesting\ValueObject\SplitLine;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @api
 */
final class TrioFixtureSplitter
{
    public function splitFileInfo(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \ECSPrefix20220220\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent
    {
        $parts = \ECSPrefix20220220\Nette\Utils\Strings::split($smartFileInfo->getContents(), \ECSPrefix20220220\Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
        $this->ensureHasThreeParts($parts, $smartFileInfo);
        return new \ECSPrefix20220220\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent($parts[0], $parts[1], $parts[2]);
    }
    /**
     * @param mixed[] $parts
     */
    private function ensureHasThreeParts(array $parts, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : void
    {
        if (\count($parts) === 3) {
            return;
        }
        $message = \sprintf('The fixture "%s" should have 3 parts. %d found', $smartFileInfo->getRelativeFilePathFromCwd(), \count($parts));
        throw new \ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException($message);
    }
}
