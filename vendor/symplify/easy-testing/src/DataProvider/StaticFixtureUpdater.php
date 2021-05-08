<?php

namespace Symplify\EasyTesting\DataProvider;

use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
final class StaticFixtureUpdater
{
    /**
     * @return void
     * @param string $changedContent
     */
    public static function updateFixtureContent(\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, $changedContent, \Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo)
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
     */
    public static function updateExpectedFixtureContent($newOriginalContent, \Symplify\SmartFileSystem\SmartFileInfo $expectedFixtureFileInfo)
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
     * @param string $changedContent
     * @return string
     */
    private static function resolveNewFixtureContent(\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, $changedContent)
    {
        if ($originalFileInfo->getContents() === $changedContent) {
            return $originalFileInfo->getContents();
        }
        return $originalFileInfo->getContents() . '-----' . \PHP_EOL . $changedContent;
    }
}
