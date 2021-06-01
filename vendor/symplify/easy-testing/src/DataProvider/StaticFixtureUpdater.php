<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\EasyTesting\DataProvider;

use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem;
final class StaticFixtureUpdater
{
    /**
     * @return void
     */
    public static function updateFixtureContent(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, string $changedContent, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo)
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
    public static function updateExpectedFixtureContent(string $newOriginalContent, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $expectedFixtureFileInfo)
    {
        if (!\getenv('UPDATE_TESTS') && !\getenv('UT')) {
            return;
        }
        self::getSmartFileSystem()->dumpFile($expectedFixtureFileInfo->getRealPath(), $newOriginalContent);
    }
    private static function getSmartFileSystem() : \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem
    {
        return new \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem();
    }
    private static function resolveNewFixtureContent(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $originalFileInfo, string $changedContent) : string
    {
        if ($originalFileInfo->getContents() === $changedContent) {
            return $originalFileInfo->getContents();
        }
        return $originalFileInfo->getContents() . '-----' . \PHP_EOL . $changedContent;
    }
}
