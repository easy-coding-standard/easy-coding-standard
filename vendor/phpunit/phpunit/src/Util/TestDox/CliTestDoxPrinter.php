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
namespace ECSPrefix20210804\PHPUnit\Util\TestDox;

use const PHP_EOL;
use function array_map;
use function ceil;
use function count;
use function explode;
use function get_class;
use function implode;
use function preg_match;
use function sprintf;
use function strlen;
use function strpos;
use function trim;
use ECSPrefix20210804\PHPUnit\Framework\Test;
use ECSPrefix20210804\PHPUnit\Framework\TestCase;
use ECSPrefix20210804\PHPUnit\Framework\TestResult;
use ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner;
use ECSPrefix20210804\PHPUnit\Runner\PhptTestCase;
use ECSPrefix20210804\PHPUnit\Util\Color;
use ECSPrefix20210804\SebastianBergmann\Timer\ResourceUsageFormatter;
use ECSPrefix20210804\SebastianBergmann\Timer\Timer;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
class CliTestDoxPrinter extends \ECSPrefix20210804\PHPUnit\Util\TestDox\TestDoxPrinter
{
    /**
     * The default Testdox left margin for messages is a vertical line.
     */
    private const PREFIX_SIMPLE = ['default' => '│', 'start' => '│', 'message' => '│', 'diff' => '│', 'trace' => '│', 'last' => '│'];
    /**
     * Colored Testdox use box-drawing for a more textured map of the message.
     */
    private const PREFIX_DECORATED = ['default' => '│', 'start' => '┐', 'message' => '├', 'diff' => '┊', 'trace' => '╵', 'last' => '┴'];
    private const SPINNER_ICONS = [" \33[36m◐\33[0m running tests", " \33[36m◓\33[0m running tests", " \33[36m◑\33[0m running tests", " \33[36m◒\33[0m running tests"];
    private const STATUS_STYLES = [\ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_PASSED => ['symbol' => '✔', 'color' => 'fg-green'], \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_ERROR => ['symbol' => '✘', 'color' => 'fg-yellow', 'message' => 'bg-yellow,fg-black'], \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_FAILURE => ['symbol' => '✘', 'color' => 'fg-red', 'message' => 'bg-red,fg-white'], \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_SKIPPED => ['symbol' => '↩', 'color' => 'fg-cyan', 'message' => 'fg-cyan'], \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_RISKY => ['symbol' => '☢', 'color' => 'fg-yellow', 'message' => 'fg-yellow'], \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_INCOMPLETE => ['symbol' => '∅', 'color' => 'fg-yellow', 'message' => 'fg-yellow'], \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_WARNING => ['symbol' => '⚠', 'color' => 'fg-yellow', 'message' => 'fg-yellow'], \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_UNKNOWN => ['symbol' => '?', 'color' => 'fg-blue', 'message' => 'fg-white,bg-blue']];
    /**
     * @var int[]
     */
    private $nonSuccessfulTestResults = [];
    /**
     * @var Timer
     */
    private $timer;
    /**
     * @param null|resource|string $out
     * @param int|string           $numberOfColumns
     *
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct($out = null, bool $verbose = \false, string $colors = self::COLOR_DEFAULT, bool $debug = \false, $numberOfColumns = 80, bool $reverse = \false)
    {
        parent::__construct($out, $verbose, $colors, $debug, $numberOfColumns, $reverse);
        $this->timer = new \ECSPrefix20210804\SebastianBergmann\Timer\Timer();
        $this->timer->start();
    }
    public function printResult(\ECSPrefix20210804\PHPUnit\Framework\TestResult $result) : void
    {
        $this->printHeader($result);
        $this->printNonSuccessfulTestsSummary($result->count());
        $this->printFooter($result);
    }
    protected function printHeader(\ECSPrefix20210804\PHPUnit\Framework\TestResult $result) : void
    {
        $this->write("\n" . (new \ECSPrefix20210804\SebastianBergmann\Timer\ResourceUsageFormatter())->resourceUsage($this->timer->stop()) . "\n\n");
    }
    protected function formatClassName(\ECSPrefix20210804\PHPUnit\Framework\Test $test) : string
    {
        if ($test instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase) {
            return $this->prettifier->prettifyTestClass(\get_class($test));
        }
        return \get_class($test);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function registerTestResult(\ECSPrefix20210804\PHPUnit\Framework\Test $test, ?\Throwable $t, int $status, float $time, bool $verbose) : void
    {
        if ($status !== \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_PASSED) {
            $this->nonSuccessfulTestResults[] = $this->testIndex;
        }
        parent::registerTestResult($test, $t, $status, $time, $verbose);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function formatTestName(\ECSPrefix20210804\PHPUnit\Framework\Test $test) : string
    {
        if ($test instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase) {
            return $this->prettifier->prettifyTestCase($test);
        }
        return parent::formatTestName($test);
    }
    protected function writeTestResult(array $prevResult, array $result) : void
    {
        // spacer line for new suite headers and after verbose messages
        if ($prevResult['testName'] !== '' && (!empty($prevResult['message']) || $prevResult['className'] !== $result['className'])) {
            $this->write(\PHP_EOL);
        }
        // suite header
        if ($prevResult['className'] !== $result['className']) {
            $this->write($this->colorizeTextBox('underlined', $result['className']) . \PHP_EOL);
        }
        // test result line
        if ($this->colors && $result['className'] === \ECSPrefix20210804\PHPUnit\Runner\PhptTestCase::class) {
            $testName = \ECSPrefix20210804\PHPUnit\Util\Color::colorizePath($result['testName'], $prevResult['testName'], \true);
        } else {
            $testName = $result['testMethod'];
        }
        $style = self::STATUS_STYLES[$result['status']];
        $line = \sprintf(' %s %s%s' . \PHP_EOL, $this->colorizeTextBox($style['color'], $style['symbol']), $testName, $this->verbose ? ' ' . $this->formatRuntime($result['time'], $style['color']) : '');
        $this->write($line);
        // additional information when verbose
        $this->write($result['message']);
    }
    protected function formatThrowable(\Throwable $t, ?int $status = null) : string
    {
        return \trim(\ECSPrefix20210804\PHPUnit\Framework\TestFailure::exceptionToString($t));
    }
    protected function colorizeMessageAndDiff(string $style, string $buffer) : array
    {
        $lines = $buffer ? \array_map('\\rtrim', \explode(\PHP_EOL, $buffer)) : [];
        $message = [];
        $diff = [];
        $insideDiff = \false;
        foreach ($lines as $line) {
            if ($line === '--- Expected') {
                $insideDiff = \true;
            }
            if (!$insideDiff) {
                $message[] = $line;
            } else {
                if (\strpos($line, '-') === 0) {
                    $line = \ECSPrefix20210804\PHPUnit\Util\Color::colorize('fg-red', \ECSPrefix20210804\PHPUnit\Util\Color::visualizeWhitespace($line, \true));
                } elseif (\strpos($line, '+') === 0) {
                    $line = \ECSPrefix20210804\PHPUnit\Util\Color::colorize('fg-green', \ECSPrefix20210804\PHPUnit\Util\Color::visualizeWhitespace($line, \true));
                } elseif ($line === '@@ @@') {
                    $line = \ECSPrefix20210804\PHPUnit\Util\Color::colorize('fg-cyan', $line);
                }
                $diff[] = $line;
            }
        }
        $diff = \implode(\PHP_EOL, $diff);
        if (!empty($message)) {
            $message = $this->colorizeTextBox($style, \implode(\PHP_EOL, $message));
        }
        return [$message, $diff];
    }
    protected function formatStacktrace(\Throwable $t) : string
    {
        $trace = \ECSPrefix20210804\PHPUnit\Util\Filter::getFilteredStacktrace($t);
        if (!$this->colors) {
            return $trace;
        }
        $lines = [];
        $prevPath = '';
        foreach (\explode(\PHP_EOL, $trace) as $line) {
            if (\preg_match('/^(.*):(\\d+)$/', $line, $matches)) {
                $lines[] = \ECSPrefix20210804\PHPUnit\Util\Color::colorizePath($matches[1], $prevPath) . \ECSPrefix20210804\PHPUnit\Util\Color::dim(':') . \ECSPrefix20210804\PHPUnit\Util\Color::colorize('fg-blue', $matches[2]) . "\n";
                $prevPath = $matches[1];
            } else {
                $lines[] = $line;
                $prevPath = '';
            }
        }
        return \implode('', $lines);
    }
    protected function formatTestResultMessage(\Throwable $t, array $result, ?string $prefix = null) : string
    {
        $message = $this->formatThrowable($t, $result['status']);
        $diff = '';
        if (!($this->verbose || $result['verbose'])) {
            return '';
        }
        if ($message && $this->colors) {
            $style = self::STATUS_STYLES[$result['status']]['message'] ?? '';
            [$message, $diff] = $this->colorizeMessageAndDiff($style, $message);
        }
        if ($prefix === null || !$this->colors) {
            $prefix = self::PREFIX_SIMPLE;
        }
        if ($this->colors) {
            $color = self::STATUS_STYLES[$result['status']]['color'] ?? '';
            $prefix = \array_map(static function ($p) use($color) {
                return \ECSPrefix20210804\PHPUnit\Util\Color::colorize($color, $p);
            }, self::PREFIX_DECORATED);
        }
        $trace = $this->formatStacktrace($t);
        $out = $this->prefixLines($prefix['start'], \PHP_EOL) . \PHP_EOL;
        if ($message) {
            $out .= $this->prefixLines($prefix['message'], $message . \PHP_EOL) . \PHP_EOL;
        }
        if ($diff) {
            $out .= $this->prefixLines($prefix['diff'], $diff . \PHP_EOL) . \PHP_EOL;
        }
        if ($trace) {
            if ($message || $diff) {
                $out .= $this->prefixLines($prefix['default'], \PHP_EOL) . \PHP_EOL;
            }
            $out .= $this->prefixLines($prefix['trace'], $trace . \PHP_EOL) . \PHP_EOL;
        }
        $out .= $this->prefixLines($prefix['last'], \PHP_EOL) . \PHP_EOL;
        return $out;
    }
    protected function drawSpinner() : void
    {
        if ($this->colors) {
            $id = $this->spinState % \count(self::SPINNER_ICONS);
            $this->write(self::SPINNER_ICONS[$id]);
        }
    }
    protected function undrawSpinner() : void
    {
        if ($this->colors) {
            $id = $this->spinState % \count(self::SPINNER_ICONS);
            $this->write("\33[1K\33[" . \strlen(self::SPINNER_ICONS[$id]) . 'D');
        }
    }
    private function formatRuntime(float $time, string $color = '') : string
    {
        if (!$this->colors) {
            return \sprintf('[%.2f ms]', $time * 1000);
        }
        if ($time > 1) {
            $color = 'fg-magenta';
        }
        return \ECSPrefix20210804\PHPUnit\Util\Color::colorize($color, ' ' . (int) \ceil($time * 1000) . ' ' . \ECSPrefix20210804\PHPUnit\Util\Color::dim('ms'));
    }
    private function printNonSuccessfulTestsSummary(int $numberOfExecutedTests) : void
    {
        if (empty($this->nonSuccessfulTestResults)) {
            return;
        }
        if (\count($this->nonSuccessfulTestResults) / $numberOfExecutedTests >= 0.7) {
            return;
        }
        $this->write("Summary of non-successful tests:\n\n");
        $prevResult = $this->getEmptyTestResult();
        foreach ($this->nonSuccessfulTestResults as $testIndex) {
            $result = $this->testResults[$testIndex];
            $this->writeTestResult($prevResult, $result);
            $prevResult = $result;
        }
    }
}
