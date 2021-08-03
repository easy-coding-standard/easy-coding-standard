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
namespace ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis;

use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter;
final class CacheWarmer
{
    public function warmCache(string $cacheDirectory, bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecatedCode, \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter $filter) : void
    {
        $coveredFileAnalyser = new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\CachingCoveredFileAnalyser($cacheDirectory, new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingCoveredFileAnalyser($useAnnotationsForIgnoringCode, $ignoreDeprecatedCode));
        $uncoveredFileAnalyser = new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\CachingUncoveredFileAnalyser($cacheDirectory, new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingUncoveredFileAnalyser());
        foreach ($filter->files() as $file) {
            $coveredFileAnalyser->process($file);
            /* @noinspection UnusedFunctionResultInspection */
            $uncoveredFileAnalyser->executableLinesIn($file);
        }
    }
}
