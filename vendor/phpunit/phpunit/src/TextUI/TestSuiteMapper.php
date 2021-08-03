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
namespace ECSPrefix20210803\PHPUnit\TextUI;

use const PHP_VERSION;
use function explode;
use function in_array;
use function is_dir;
use function is_file;
use function strpos;
use function version_compare;
use ECSPrefix20210803\PHPUnit\Framework\Exception as FrameworkException;
use ECSPrefix20210803\PHPUnit\Framework\TestSuite as TestSuiteObject;
use ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\TestSuiteCollection;
use ECSPrefix20210803\SebastianBergmann\FileIterator\Facade;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestSuiteMapper
{
    /**
     * @throws RuntimeException
     * @throws TestDirectoryNotFoundException
     * @throws TestFileNotFoundException
     */
    public function map(\ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\TestSuiteCollection $configuration, string $filter) : \ECSPrefix20210803\PHPUnit\Framework\TestSuite
    {
        try {
            $filterAsArray = $filter ? \explode(',', $filter) : [];
            $result = new \ECSPrefix20210803\PHPUnit\Framework\TestSuite();
            foreach ($configuration as $testSuiteConfiguration) {
                if (!empty($filterAsArray) && !\in_array($testSuiteConfiguration->name(), $filterAsArray, \true)) {
                    continue;
                }
                $testSuite = new \ECSPrefix20210803\PHPUnit\Framework\TestSuite($testSuiteConfiguration->name());
                $testSuiteEmpty = \true;
                foreach ($testSuiteConfiguration->directories() as $directory) {
                    if (!\version_compare(\PHP_VERSION, $directory->phpVersion(), $directory->phpVersionOperator()->asString())) {
                        continue;
                    }
                    $exclude = [];
                    foreach ($testSuiteConfiguration->exclude()->asArray() as $file) {
                        $exclude[] = $file->path();
                    }
                    $files = (new \ECSPrefix20210803\SebastianBergmann\FileIterator\Facade())->getFilesAsArray($directory->path(), $directory->suffix(), $directory->prefix(), $exclude);
                    if (!empty($files)) {
                        $testSuite->addTestFiles($files);
                        $testSuiteEmpty = \false;
                    } elseif (\strpos($directory->path(), '*') === \false && !\is_dir($directory->path())) {
                        throw new \ECSPrefix20210803\PHPUnit\TextUI\TestDirectoryNotFoundException($directory->path());
                    }
                }
                foreach ($testSuiteConfiguration->files() as $file) {
                    if (!\is_file($file->path())) {
                        throw new \ECSPrefix20210803\PHPUnit\TextUI\TestFileNotFoundException($file->path());
                    }
                    if (!\version_compare(\PHP_VERSION, $file->phpVersion(), $file->phpVersionOperator()->asString())) {
                        continue;
                    }
                    $testSuite->addTestFile($file->path());
                    $testSuiteEmpty = \false;
                }
                if (!$testSuiteEmpty) {
                    $result->addTest($testSuite);
                }
            }
            return $result;
        } catch (\ECSPrefix20210803\PHPUnit\Framework\Exception $e) {
            throw new \ECSPrefix20210803\PHPUnit\TextUI\RuntimeException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
