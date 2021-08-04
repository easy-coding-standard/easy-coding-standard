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
namespace ECSPrefix20210804\PHPUnit\Util;

/**
 * @deprecated Use ExcludeList instead
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class Blacklist
{
    public static function addDirectory(string $directory) : void
    {
        \ECSPrefix20210804\PHPUnit\Util\ExcludeList::addDirectory($directory);
    }
    /**
     * @throws Exception
     *
     * @return string[]
     */
    public function getBlacklistedDirectories() : array
    {
        return (new \ECSPrefix20210804\PHPUnit\Util\ExcludeList())->getExcludedDirectories();
    }
    /**
     * @throws Exception
     */
    public function isBlacklisted(string $file) : bool
    {
        return (new \ECSPrefix20210804\PHPUnit\Util\ExcludeList())->isExcluded($file);
    }
}
