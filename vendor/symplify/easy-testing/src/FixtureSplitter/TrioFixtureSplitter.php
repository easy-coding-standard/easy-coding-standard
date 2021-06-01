<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\EasyTesting\FixtureSplitter;

use ConfigTransformer20210601\Nette\Utils\Strings;
use ConfigTransformer20210601\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent;
use ConfigTransformer20210601\Symplify\EasyTesting\ValueObject\SplitLine;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
use ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class TrioFixtureSplitter
{
    public function splitFileInfo(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : \ConfigTransformer20210601\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent
    {
        $parts = \ConfigTransformer20210601\Nette\Utils\Strings::split($smartFileInfo->getContents(), \ConfigTransformer20210601\Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
        $this->ensureHasThreeParts($parts, $smartFileInfo);
        return new \ConfigTransformer20210601\Symplify\EasyTesting\ValueObject\FixtureSplit\TrioContent($parts[0], $parts[1], $parts[2]);
    }
    /**
     * @param mixed[] $parts
     * @return void
     */
    private function ensureHasThreeParts(array $parts, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo)
    {
        if (\count($parts) === 3) {
            return;
        }
        $message = \sprintf('The fixture "%s" should have 3 parts. %d found', $smartFileInfo->getRelativeFilePathFromCwd(), \count($parts));
        throw new \ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\ShouldNotHappenException($message);
    }
}
