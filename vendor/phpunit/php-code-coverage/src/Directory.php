<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\CodeCoverage;

use function is_dir;
use function mkdir;
use function sprintf;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Directory
{
    /**
     * @throws DirectoryCouldNotBeCreatedException
     */
    public static function create(string $directory) : void
    {
        $success = !(!\is_dir($directory) && !@\mkdir($directory, 0777, \true) && !\is_dir($directory));
        if (!$success) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\DirectoryCouldNotBeCreatedException(\sprintf('Directory "%s" could not be created', $directory));
        }
    }
}
