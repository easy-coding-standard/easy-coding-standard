<?php

declare (strict_types=1);
namespace ECSPrefix20220305\Symplify\EasyTesting;

use ECSPrefix20220305\Nette\Utils\Strings;
use ECSPrefix20220305\Symplify\EasyTesting\ValueObject\IncorrectAndMissingSkips;
use ECSPrefix20220305\Symplify\EasyTesting\ValueObject\Prefix;
use ECSPrefix20220305\Symplify\EasyTesting\ValueObject\SplitLine;
use ECSPrefix20220305\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyTesting\Tests\MissingSkipPrefixResolver\MissingSkipPrefixResolverTest
 */
final class MissplacedSkipPrefixResolver
{
    /**
     * @param SmartFileInfo[] $fixtureFileInfos
     */
    public function resolve(array $fixtureFileInfos) : \ECSPrefix20220305\Symplify\EasyTesting\ValueObject\IncorrectAndMissingSkips
    {
        $incorrectSkips = [];
        $missingSkips = [];
        foreach ($fixtureFileInfos as $fixtureFileInfo) {
            $hasNameSkipStart = $this->hasNameSkipStart($fixtureFileInfo);
            $fileContents = $fixtureFileInfo->getContents();
            $hasSplitLine = (bool) \ECSPrefix20220305\Nette\Utils\Strings::match($fileContents, \ECSPrefix20220305\Symplify\EasyTesting\ValueObject\SplitLine::SPLIT_LINE_REGEX);
            if ($hasNameSkipStart && $hasSplitLine) {
                $incorrectSkips[] = $fixtureFileInfo;
                continue;
            }
            if (!$hasNameSkipStart && !$hasSplitLine) {
                $missingSkips[] = $fixtureFileInfo;
            }
        }
        return new \ECSPrefix20220305\Symplify\EasyTesting\ValueObject\IncorrectAndMissingSkips($incorrectSkips, $missingSkips);
    }
    private function hasNameSkipStart(\ECSPrefix20220305\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo) : bool
    {
        return (bool) \ECSPrefix20220305\Nette\Utils\Strings::match($fixtureFileInfo->getBasenameWithoutSuffix(), \ECSPrefix20220305\Symplify\EasyTesting\ValueObject\Prefix::SKIP_PREFIX_REGEX);
    }
}
