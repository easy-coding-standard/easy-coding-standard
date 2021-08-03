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
namespace ECSPrefix20210803\PHPUnit\Util;

use const DIRECTORY_SEPARATOR;
use function class_exists;
use function defined;
use function dirname;
use function is_dir;
use function realpath;
use function sprintf;
use function strpos;
use function sys_get_temp_dir;
use ECSPrefix20210803\Composer\Autoload\ClassLoader;
use ECSPrefix20210803\DeepCopy\DeepCopy;
use ECSPrefix20210803\Doctrine\Instantiator\Instantiator;
use ECSPrefix20210803\PharIo\Manifest\Manifest;
use ECSPrefix20210803\PharIo\Version\Version as PharIoVersion;
use ECSPrefix20210803\phpDocumentor\Reflection\DocBlock;
use ECSPrefix20210803\phpDocumentor\Reflection\Project;
use ECSPrefix20210803\phpDocumentor\Reflection\Type;
use ECSPrefix20210803\PhpParser\Parser;
use ECSPrefix20210803\PHPUnit\Framework\TestCase;
use ECSPrefix20210803\Prophecy\Prophet;
use ReflectionClass;
use ReflectionException;
use ECSPrefix20210803\SebastianBergmann\CliParser\Parser as CliParser;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\CodeCoverage;
use ECSPrefix20210803\SebastianBergmann\CodeUnit\CodeUnit;
use ECSPrefix20210803\SebastianBergmann\CodeUnitReverseLookup\Wizard;
use ECSPrefix20210803\SebastianBergmann\Comparator\Comparator;
use ECSPrefix20210803\SebastianBergmann\Complexity\Calculator;
use ECSPrefix20210803\SebastianBergmann\Diff\Diff;
use ECSPrefix20210803\SebastianBergmann\Environment\Runtime;
use ECSPrefix20210803\SebastianBergmann\Exporter\Exporter;
use ECSPrefix20210803\SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
use ECSPrefix20210803\SebastianBergmann\GlobalState\Snapshot;
use ECSPrefix20210803\SebastianBergmann\Invoker\Invoker;
use ECSPrefix20210803\SebastianBergmann\LinesOfCode\Counter;
use ECSPrefix20210803\SebastianBergmann\ObjectEnumerator\Enumerator;
use ECSPrefix20210803\SebastianBergmann\RecursionContext\Context;
use ECSPrefix20210803\SebastianBergmann\ResourceOperations\ResourceOperations;
use ECSPrefix20210803\SebastianBergmann\Template\Template;
use ECSPrefix20210803\SebastianBergmann\Timer\Timer;
use ECSPrefix20210803\SebastianBergmann\Type\TypeName;
use ECSPrefix20210803\SebastianBergmann\Version;
use ECSPrefix20210803\Symfony\Polyfill\Ctype\Ctype;
use ECSPrefix20210803\TheSeer\Tokenizer\Tokenizer;
use ECSPrefix20210803\Webmozart\Assert\Assert;
/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class ExcludeList
{
    /**
     * @var array<string,int>
     */
    private const EXCLUDED_CLASS_NAMES = [
        // composer
        \ECSPrefix20210803\Composer\Autoload\ClassLoader::class => 1,
        // doctrine/instantiator
        \ECSPrefix20210803\Doctrine\Instantiator\Instantiator::class => 1,
        // myclabs/deepcopy
        \ECSPrefix20210803\DeepCopy\DeepCopy::class => 1,
        // nikic/php-parser
        \ECSPrefix20210803\PhpParser\Parser::class => 1,
        // phar-io/manifest
        \ECSPrefix20210803\PharIo\Manifest\Manifest::class => 1,
        // phar-io/version
        \ECSPrefix20210803\PharIo\Version\Version::class => 1,
        // phpdocumentor/reflection-common
        \ECSPrefix20210803\phpDocumentor\Reflection\Project::class => 1,
        // phpdocumentor/reflection-docblock
        \ECSPrefix20210803\phpDocumentor\Reflection\DocBlock::class => 1,
        // phpdocumentor/type-resolver
        \ECSPrefix20210803\phpDocumentor\Reflection\Type::class => 1,
        // phpspec/prophecy
        \ECSPrefix20210803\Prophecy\Prophet::class => 1,
        // phpunit/phpunit
        \ECSPrefix20210803\PHPUnit\Framework\TestCase::class => 2,
        // phpunit/php-code-coverage
        \ECSPrefix20210803\SebastianBergmann\CodeCoverage\CodeCoverage::class => 1,
        // phpunit/php-file-iterator
        \ECSPrefix20210803\SebastianBergmann\FileIterator\Facade::class => 1,
        // phpunit/php-invoker
        \ECSPrefix20210803\SebastianBergmann\Invoker\Invoker::class => 1,
        // phpunit/php-text-template
        \ECSPrefix20210803\SebastianBergmann\Template\Template::class => 1,
        // phpunit/php-timer
        \ECSPrefix20210803\SebastianBergmann\Timer\Timer::class => 1,
        // sebastian/cli-parser
        \ECSPrefix20210803\SebastianBergmann\CliParser\Parser::class => 1,
        // sebastian/code-unit
        \ECSPrefix20210803\SebastianBergmann\CodeUnit\CodeUnit::class => 1,
        // sebastian/code-unit-reverse-lookup
        \ECSPrefix20210803\SebastianBergmann\CodeUnitReverseLookup\Wizard::class => 1,
        // sebastian/comparator
        \ECSPrefix20210803\SebastianBergmann\Comparator\Comparator::class => 1,
        // sebastian/complexity
        \ECSPrefix20210803\SebastianBergmann\Complexity\Calculator::class => 1,
        // sebastian/diff
        \ECSPrefix20210803\SebastianBergmann\Diff\Diff::class => 1,
        // sebastian/environment
        \ECSPrefix20210803\SebastianBergmann\Environment\Runtime::class => 1,
        // sebastian/exporter
        \ECSPrefix20210803\SebastianBergmann\Exporter\Exporter::class => 1,
        // sebastian/global-state
        \ECSPrefix20210803\SebastianBergmann\GlobalState\Snapshot::class => 1,
        // sebastian/lines-of-code
        \ECSPrefix20210803\SebastianBergmann\LinesOfCode\Counter::class => 1,
        // sebastian/object-enumerator
        \ECSPrefix20210803\SebastianBergmann\ObjectEnumerator\Enumerator::class => 1,
        // sebastian/recursion-context
        \ECSPrefix20210803\SebastianBergmann\RecursionContext\Context::class => 1,
        // sebastian/resource-operations
        \ECSPrefix20210803\SebastianBergmann\ResourceOperations\ResourceOperations::class => 1,
        // sebastian/type
        \ECSPrefix20210803\SebastianBergmann\Type\TypeName::class => 1,
        // sebastian/version
        \ECSPrefix20210803\SebastianBergmann\Version::class => 1,
        // symfony/polyfill-ctype
        \ECSPrefix20210803\Symfony\Polyfill\Ctype\Ctype::class => 1,
        // theseer/tokenizer
        \ECSPrefix20210803\TheSeer\Tokenizer\Tokenizer::class => 1,
        // webmozart/assert
        \ECSPrefix20210803\Webmozart\Assert\Assert::class => 1,
    ];
    /**
     * @var string[]
     */
    private static $directories;
    public static function addDirectory(string $directory) : void
    {
        if (!\is_dir($directory)) {
            throw new \ECSPrefix20210803\PHPUnit\Util\Exception(\sprintf('"%s" is not a directory', $directory));
        }
        self::$directories[] = \realpath($directory);
    }
    /**
     * @throws Exception
     *
     * @return string[]
     */
    public function getExcludedDirectories() : array
    {
        $this->initialize();
        return self::$directories;
    }
    /**
     * @throws Exception
     */
    public function isExcluded(string $file) : bool
    {
        if (\defined('PHPUNIT_TESTSUITE')) {
            return \false;
        }
        $this->initialize();
        foreach (self::$directories as $directory) {
            if (\strpos($file, $directory) === 0) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @throws Exception
     */
    private function initialize() : void
    {
        if (self::$directories === null) {
            self::$directories = [];
            foreach (self::EXCLUDED_CLASS_NAMES as $className => $parent) {
                if (!\class_exists($className)) {
                    continue;
                }
                try {
                    $directory = (new \ReflectionClass($className))->getFileName();
                    // @codeCoverageIgnoreStart
                } catch (\ReflectionException $e) {
                    throw new \ECSPrefix20210803\PHPUnit\Util\Exception($e->getMessage(), (int) $e->getCode(), $e);
                }
                // @codeCoverageIgnoreEnd
                for ($i = 0; $i < $parent; $i++) {
                    $directory = \dirname($directory);
                }
                self::$directories[] = $directory;
            }
            // Hide process isolation workaround on Windows.
            if (\DIRECTORY_SEPARATOR === '\\') {
                // tempnam() prefix is limited to first 3 chars.
                // @see https://php.net/manual/en/function.tempnam.php
                self::$directories[] = \sys_get_temp_dir() . '\\PHP';
            }
        }
    }
}
