<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration;

use function array_key_exists;
use function sprintf;
use function version_compare;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class MigrationBuilder
{
    private const AVAILABLE_MIGRATIONS = ['8.5' => [\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\RemoveLogTypes::class], '9.2' => [\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\RemoveCacheTokensAttribute::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\IntroduceCoverageElement::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\MoveAttributesFromRootToCoverage::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\MoveAttributesFromFilterWhitelistToCoverage::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\MoveWhitelistDirectoriesToCoverage::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\MoveWhitelistExcludesToCoverage::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\RemoveEmptyFilter::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CoverageCloverToReport::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CoverageCrap4jToReport::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CoverageHtmlToReport::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CoveragePhpToReport::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CoverageTextToReport::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CoverageXmlToReport::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\ConvertLogTypes::class, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\UpdateSchemaLocationTo93::class]];
    /**
     * @throws MigrationBuilderException
     */
    public function build(string $fromVersion) : array
    {
        if (!\array_key_exists($fromVersion, self::AVAILABLE_MIGRATIONS)) {
            throw new \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\MigrationBuilderException(\sprintf('Migration from schema version %s is not supported', $fromVersion));
        }
        $stack = [];
        foreach (self::AVAILABLE_MIGRATIONS as $version => $migrations) {
            if (\version_compare($version, $fromVersion, '<')) {
                continue;
            }
            foreach ($migrations as $migration) {
                $stack[] = new $migration();
            }
        }
        return $stack;
    }
}
