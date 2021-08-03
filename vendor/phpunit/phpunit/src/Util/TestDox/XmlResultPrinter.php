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
namespace ECSPrefix20210803\PHPUnit\Util\TestDox;

use function array_filter;
use function get_class;
use function implode;
use function strpos;
use DOMDocument;
use DOMElement;
use ECSPrefix20210803\PHPUnit\Framework\AssertionFailedError;
use ECSPrefix20210803\PHPUnit\Framework\Exception;
use ECSPrefix20210803\PHPUnit\Framework\Test;
use ECSPrefix20210803\PHPUnit\Framework\TestCase;
use ECSPrefix20210803\PHPUnit\Framework\TestListener;
use ECSPrefix20210803\PHPUnit\Framework\TestSuite;
use ECSPrefix20210803\PHPUnit\Framework\Warning;
use ECSPrefix20210803\PHPUnit\Framework\WarningTestCase;
use ECSPrefix20210803\PHPUnit\Util\Printer;
use ECSPrefix20210803\PHPUnit\Util\Test as TestUtil;
use ReflectionClass;
use ReflectionException;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class XmlResultPrinter extends \ECSPrefix20210803\PHPUnit\Util\Printer implements \ECSPrefix20210803\PHPUnit\Framework\TestListener
{
    /**
     * @var DOMDocument
     */
    private $document;
    /**
     * @var DOMElement
     */
    private $root;
    /**
     * @var NamePrettifier
     */
    private $prettifier;
    /**
     * @var null|Throwable
     */
    private $exception;
    /**
     * @param resource|string $out
     *
     * @throws Exception
     */
    public function __construct($out = null)
    {
        $this->document = new \DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = \true;
        $this->root = $this->document->createElement('tests');
        $this->document->appendChild($this->root);
        $this->prettifier = new \ECSPrefix20210803\PHPUnit\Util\TestDox\NamePrettifier();
        parent::__construct($out);
    }
    /**
     * Flush buffer and close output.
     */
    public function flush() : void
    {
        $this->write($this->document->saveXML());
        parent::flush();
    }
    /**
     * An error occurred.
     */
    public function addError(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->exception = $t;
    }
    /**
     * A warning occurred.
     */
    public function addWarning(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \ECSPrefix20210803\PHPUnit\Framework\Warning $e, float $time) : void
    {
    }
    /**
     * A failure occurred.
     */
    public function addFailure(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \ECSPrefix20210803\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        $this->exception = $e;
    }
    /**
     * Incomplete test.
     */
    public function addIncompleteTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    /**
     * Risky test.
     */
    public function addRiskyTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    /**
     * Skipped test.
     */
    public function addSkippedTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    /**
     * A test suite started.
     */
    public function startTestSuite(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    /**
     * A test suite ended.
     */
    public function endTestSuite(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    /**
     * A test started.
     */
    public function startTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test) : void
    {
        $this->exception = null;
    }
    /**
     * A test ended.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function endTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, float $time) : void
    {
        if (!$test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase || $test instanceof \ECSPrefix20210803\PHPUnit\Framework\WarningTestCase) {
            return;
        }
        $groups = \array_filter($test->getGroups(), static function ($group) {
            return !($group === 'small' || $group === 'medium' || $group === 'large' || \strpos($group, '__phpunit_') === 0);
        });
        $testNode = $this->document->createElement('test');
        $testNode->setAttribute('className', \get_class($test));
        $testNode->setAttribute('methodName', $test->getName());
        $testNode->setAttribute('prettifiedClassName', $this->prettifier->prettifyTestClass(\get_class($test)));
        $testNode->setAttribute('prettifiedMethodName', $this->prettifier->prettifyTestCase($test));
        $testNode->setAttribute('status', (string) $test->getStatus());
        $testNode->setAttribute('time', (string) $time);
        $testNode->setAttribute('size', (string) $test->getSize());
        $testNode->setAttribute('groups', \implode(',', $groups));
        foreach ($groups as $group) {
            $groupNode = $this->document->createElement('group');
            $groupNode->setAttribute('name', $group);
            $testNode->appendChild($groupNode);
        }
        $annotations = \ECSPrefix20210803\PHPUnit\Util\Test::parseTestMethodAnnotations(\get_class($test), $test->getName(\false));
        foreach (['class', 'method'] as $type) {
            foreach ($annotations[$type] as $annotation => $values) {
                if ($annotation !== 'covers' && $annotation !== 'uses') {
                    continue;
                }
                foreach ($values as $value) {
                    $coversNode = $this->document->createElement($annotation);
                    $coversNode->setAttribute('target', $value);
                    $testNode->appendChild($coversNode);
                }
            }
        }
        foreach ($test->doubledTypes() as $doubledType) {
            $testDoubleNode = $this->document->createElement('testDouble');
            $testDoubleNode->setAttribute('type', $doubledType);
            $testNode->appendChild($testDoubleNode);
        }
        $inlineAnnotations = \ECSPrefix20210803\PHPUnit\Util\Test::getInlineAnnotations(\get_class($test), $test->getName(\false));
        if (isset($inlineAnnotations['given'], $inlineAnnotations['when'], $inlineAnnotations['then'])) {
            $testNode->setAttribute('given', $inlineAnnotations['given']['value']);
            $testNode->setAttribute('givenStartLine', (string) $inlineAnnotations['given']['line']);
            $testNode->setAttribute('when', $inlineAnnotations['when']['value']);
            $testNode->setAttribute('whenStartLine', (string) $inlineAnnotations['when']['line']);
            $testNode->setAttribute('then', $inlineAnnotations['then']['value']);
            $testNode->setAttribute('thenStartLine', (string) $inlineAnnotations['then']['line']);
        }
        if ($this->exception !== null) {
            if ($this->exception instanceof \ECSPrefix20210803\PHPUnit\Framework\Exception) {
                $steps = $this->exception->getSerializableTrace();
            } else {
                $steps = $this->exception->getTrace();
            }
            try {
                $file = (new \ReflectionClass($test))->getFileName();
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $e) {
                throw new \ECSPrefix20210803\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
            }
            // @codeCoverageIgnoreEnd
            foreach ($steps as $step) {
                if (isset($step['file']) && $step['file'] === $file) {
                    $testNode->setAttribute('exceptionLine', (string) $step['line']);
                    break;
                }
            }
            $testNode->setAttribute('exceptionMessage', $this->exception->getMessage());
        }
        $this->root->appendChild($testNode);
    }
}
