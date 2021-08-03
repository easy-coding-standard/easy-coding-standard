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
namespace ECSPrefix20210803\SebastianBergmann\CodeCoverage\Report;

use function dirname;
use function file_put_contents;
use function serialize;
use function sprintf;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\CodeCoverage;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Directory;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\WriteOperationFailedException;
final class PHP
{
    public function process(\ECSPrefix20210803\SebastianBergmann\CodeCoverage\CodeCoverage $coverage, ?string $target = null) : string
    {
        $buffer = \sprintf("<?php\nreturn \\unserialize(<<<'END_OF_COVERAGE_SERIALIZATION'%s%s%sEND_OF_COVERAGE_SERIALIZATION%s);", \PHP_EOL, \serialize($coverage), \PHP_EOL, \PHP_EOL);
        if ($target !== null) {
            \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Directory::create(\dirname($target));
            if (@\file_put_contents($target, $buffer) === \false) {
                throw new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\WriteOperationFailedException($target);
            }
        }
        return $buffer;
    }
}
