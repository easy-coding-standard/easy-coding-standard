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
    public static function updateFixtureContent(
        SmartFileInfo $originalFileInfo,
        $changedContent,
        SmartFileInfo $fixtureFileInfo
    ) {
        $changedContent = (string) $changedContent;
        if (! getenv('UPDATE_TESTS') && ! getenv('UT')) {
            return;
        }

        $newOriginalContent = self::resolveNewFixtureContent($originalFileInfo, $changedContent);

        self::getSmartFileSystem()
            ->dumpFile($fixtureFileInfo->getRealPath(), $newOriginalContent);
    }

    /**
     * @return void
     * @param string $newOriginalContent
     */
    public static function updateExpectedFixtureContent(
        $newOriginalContent,
        SmartFileInfo $expectedFixtureFileInfo
    ) {
        $newOriginalContent = (string) $newOriginalContent;
        if (! getenv('UPDATE_TESTS') && ! getenv('UT')) {
            return;
        }

        self::getSmartFileSystem()
            ->dumpFile($expectedFixtureFileInfo->getRealPath(), $newOriginalContent);
    }

    /**
     * @return \Symplify\SmartFileSystem\SmartFileSystem
     */
    private static function getSmartFileSystem()
    {
        return new SmartFileSystem();
    }

    /**
     * @param string $changedContent
     * @return string
     */
    private static function resolveNewFixtureContent(SmartFileInfo $originalFileInfo, $changedContent)
    {
        $changedContent = (string) $changedContent;
        if ($originalFileInfo->getContents() === $changedContent) {
            return $originalFileInfo->getContents();
        }

        return $originalFileInfo->getContents() . '-----' . PHP_EOL . $changedContent;
    }
}
