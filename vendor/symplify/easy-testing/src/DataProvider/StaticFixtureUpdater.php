<?php

declare (strict_types=1);
namespace ECSPrefix20210724\Symplify\EasyTesting\DataProvider;

use ECSPrefix20210724\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210724\Symplify\SmartFileSystem\SmartFileSystem;
final class StaticFixtureUpdater
{
    /**
     * @return void
     */
    public static function updateFixtureContent(\ECSPrefix20210724\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, string $changedContent, \ECSPrefix20210724\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo)
    {
        if (!\getenv('UPDATE_TESTS') && !\getenv('UT')) {
            return;
        }
        $newOriginalContent = self::resolveNewFixtureContent($originalFileInfo, $changedContent);
        self::getSmartFileSystem()->dumpFile($fixtureFileInfo->getRealPath(), $newOriginalContent);
    }
    /**
     * @return void
     */
    public static function updateExpectedFixtureContent(string $newOriginalContent, \ECSPrefix20210724\Symplify\SmartFileSystem\SmartFileInfo $expectedFixtureFileInfo)
    {
        if (!\getenv('UPDATE_TESTS') && !\getenv('UT')) {
            return;
        }
        self::getSmartFileSystem()->dumpFile($expectedFixtureFileInfo->getRealPath(), $newOriginalContent);
    }
    private static function getSmartFileSystem() : \ECSPrefix20210724\Symplify\SmartFileSystem\SmartFileSystem
    {
        return new \ECSPrefix20210724\Symplify\SmartFileSystem\SmartFileSystem();
    }
    private static function resolveNewFixtureContent(\ECSPrefix20210724\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, string $changedContent) : string
    {
        if ($originalFileInfo->getContents() === $changedContent) {
            return $originalFileInfo->getContents();
        }
        return $originalFileInfo->getContents() . '-----' . \PHP_EOL . $changedContent;
    }
}
