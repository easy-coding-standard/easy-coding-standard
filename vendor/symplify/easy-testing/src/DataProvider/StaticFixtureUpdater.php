<?php

namespace ECSPrefix20210516\Symplify\EasyTesting\DataProvider;

use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileSystem;
final class StaticFixtureUpdater
{
    /**
     * @return void
     * @param string $changedContent
     */
    public static function updateFixtureContent(\ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, $changedContent, \ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo)
    {
        $changedContent = (string) $changedContent;
        if (!\getenv('UPDATE_TESTS') && !\getenv('UT')) {
            return;
        }
        $newOriginalContent = self::resolveNewFixtureContent($originalFileInfo, $changedContent);
        self::getSmartFileSystem()->dumpFile($fixtureFileInfo->getRealPath(), $newOriginalContent);
    }
    /**
     * @return void
     * @param string $newOriginalContent
     */
    public static function updateExpectedFixtureContent($newOriginalContent, \ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $expectedFixtureFileInfo)
    {
        $newOriginalContent = (string) $newOriginalContent;
        if (!\getenv('UPDATE_TESTS') && !\getenv('UT')) {
            return;
        }
        self::getSmartFileSystem()->dumpFile($expectedFixtureFileInfo->getRealPath(), $newOriginalContent);
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileSystem
     */
    private static function getSmartFileSystem()
    {
        return new \ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileSystem();
    }
    /**
     * @param string $changedContent
     * @return string
     */
    private static function resolveNewFixtureContent(\ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, $changedContent)
    {
        $changedContent = (string) $changedContent;
        if ($originalFileInfo->getContents() === $changedContent) {
            return $originalFileInfo->getContents();
        }
        return $originalFileInfo->getContents() . '-----' . \PHP_EOL . $changedContent;
    }
}
