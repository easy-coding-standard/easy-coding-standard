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
namespace ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\CodeCoverage;

use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class FilterMapper
{
    public function map(\ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter $filter, \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\CodeCoverage $configuration) : void
    {
        foreach ($configuration->directories() as $directory) {
            $filter->includeDirectory($directory->path(), $directory->suffix(), $directory->prefix());
        }
        foreach ($configuration->files() as $file) {
            $filter->includeFile($file->path());
        }
        foreach ($configuration->excludeDirectories() as $directory) {
            $filter->excludeDirectory($directory->path(), $directory->suffix(), $directory->prefix());
        }
        foreach ($configuration->excludeFiles() as $file) {
            $filter->excludeFile($file->path());
        }
    }
}
