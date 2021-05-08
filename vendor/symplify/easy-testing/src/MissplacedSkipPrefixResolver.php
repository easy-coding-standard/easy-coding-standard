<?php

namespace Symplify\EasyTesting;

use ECSPrefix20210508\Nette\Utils\Strings;
use Symplify\EasyTesting\ValueObject\Prefix;
use Symplify\EasyTesting\ValueObject\SplitLine;
use Symplify\SmartFileSystem\SmartFileInfo;
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
            $hasSplitLine = (bool) \ECSPrefix20210508\Nette\Utils\Strings::match($fileContents, \Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
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
    private function hasNameSkipStart(\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo)
    {
        return (bool) \ECSPrefix20210508\Nette\Utils\Strings::match($fixtureFileInfo->getBasenameWithoutSuffix(), \Symplify\EasyTesting\ValueObject\Prefix::SKIP_PREFIX_REGEX);
    }
}
