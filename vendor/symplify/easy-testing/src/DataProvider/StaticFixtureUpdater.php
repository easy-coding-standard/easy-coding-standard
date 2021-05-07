<?php

namespace Symplify\EasyTesting\DataProvider;

use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
final class StaticFixtureUpdater
{
    /**
     * @return void
     * @param \Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo
     * @param string $changedContent
     * @param \Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo
     */
    public static function updateFixtureContent($originalFileInfo, $changedContent, $fixtureFileInfo)
    {
        if (!\getenv('UPDATE_TESTS') && !\getenv('UT')) {
            return;
        }
        $newOriginalContent = self::resolveNewFixtureContent($originalFileInfo, $changedContent);
        self::getSmartFileSystem()->dumpFile($fixtureFileInfo->getRealPath(), $newOriginalContent);
    }
    /**
     * @return void
     * @param string $newOriginalContent
     * @param \Symplify\SmartFileSystem\SmartFileInfo $expectedFixtureFileInfo
     */
    public static function updateExpectedFixtureContent($newOriginalContent, $expectedFixtureFileInfo)
    {
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
        return new \Symplify\SmartFileSystem\SmartFileSystem();
    }
    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo
     * @param string $changedContent
     * @return string
     */
    private static function resolveNewFixtureContent($originalFileInfo, $changedContent)
    {
        if ($originalFileInfo->getContents() === $changedContent) {
            return $originalFileInfo->getContents();
        }
        return $originalFileInfo->getContents() . '-----' . \PHP_EOL . $changedContent;
    }
}
