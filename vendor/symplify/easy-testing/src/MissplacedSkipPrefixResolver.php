<?php

namespace ECSPrefix20210516\Symplify\EasyTesting;

use ECSPrefix20210516\Nette\Utils\Strings;
use ECSPrefix20210516\Symplify\EasyTesting\ValueObject\Prefix;
use ECSPrefix20210516\Symplify\EasyTesting\ValueObject\SplitLine;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyTesting\Tests\MissingSkipPrefixResolver\MissingSkipPrefixResolverTest
 */
final class MissplacedSkipPrefixResolver
{
    /**
     * @param SmartFileInfo[] $fixtureFileInfos
     * @return mixed[]
     */
    public function resolve(array $fixtureFileInfos)
    {
        $invalidFileInfos = ['incorrect_skips' => [], 'missing_skips' => []];
        foreach ($fixtureFileInfos as $fixtureFileInfo) {
            $hasNameSkipStart = $this->hasNameSkipStart($fixtureFileInfo);
            $fileContents = $fixtureFileInfo->getContents();
            $hasSplitLine = (bool) \ECSPrefix20210516\Nette\Utils\Strings::match($fileContents, \ECSPrefix20210516\Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
            if ($hasNameSkipStart && $hasSplitLine) {
                $invalidFileInfos['incorrect_skips'][] = $fixtureFileInfo;
                continue;
            }
            if (!$hasNameSkipStart && !$hasSplitLine) {
                $invalidFileInfos['missing_skips'][] = $fixtureFileInfo;
                continue;
            }
        }
        return $invalidFileInfos;
    }
    /**
     * @return bool
     */
    private function hasNameSkipStart(\ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo)
    {
        return (bool) \ECSPrefix20210516\Nette\Utils\Strings::match($fixtureFileInfo->getBasenameWithoutSuffix(), \ECSPrefix20210516\Symplify\EasyTesting\ValueObject\Prefix::SKIP_PREFIX_REGEX);
    }
}
