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
namespace ECSPrefix20210804\PHPUnit\Framework;

use function func_get_args;
use ArrayAccess;
use Countable;
use DOMDocument;
use DOMElement;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\ArrayHasKey;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\Callback;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\ClassHasAttribute;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\ClassHasStaticAttribute;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\Constraint;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\Count;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\DirectoryExists;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\FileExists;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\GreaterThan;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsAnything;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEmpty;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqual;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqualCanonicalizing;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqualIgnoringCase;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqualWithDelta;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsFalse;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsFinite;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsIdentical;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsInfinite;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsInstanceOf;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsJson;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsNan;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsNull;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsReadable;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsTrue;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsType;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsWritable;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\LessThan;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalAnd;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalNot;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalOr;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalXor;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\ObjectEquals;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\ObjectHasAttribute;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\RegularExpression;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\StringContains;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\StringEndsWith;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\StringStartsWith;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\TraversableContainsEqual;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\TraversableContainsIdentical;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\TraversableContainsOnly;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\AnyInvokedCount as AnyInvokedCountMatcher;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtIndex as InvokedAtIndexMatcher;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtLeastCount as InvokedAtLeastCountMatcher;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce as InvokedAtLeastOnceMatcher;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtMostCount as InvokedAtMostCountMatcher;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls as ConsecutiveCallsStub;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\Exception as ExceptionStub;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnArgument as ReturnArgumentStub;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnCallback as ReturnCallbackStub;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnSelf as ReturnSelfStub;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnStub;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnValueMap as ReturnValueMapStub;
use Throwable;
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertArrayHasKey')) {
    /**
     * Asserts that an array has a specified key.
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertArrayHasKey
     */
    function assertArrayHasKey($key, $array, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertArrayHasKey(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertArrayNotHasKey')) {
    /**
     * Asserts that an array does not have a specified key.
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertArrayNotHasKey
     */
    function assertArrayNotHasKey($key, $array, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertArrayNotHasKey(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertContains')) {
    /**
     * Asserts that a haystack contains a needle.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertContains
     */
    function assertContains($needle, iterable $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertContains(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertContainsEquals')) {
    function assertContainsEquals($needle, iterable $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertContainsEquals(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotContains')) {
    /**
     * Asserts that a haystack does not contain a needle.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotContains
     */
    function assertNotContains($needle, iterable $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotContains(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotContainsEquals')) {
    function assertNotContainsEquals($needle, iterable $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotContainsEquals(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertContainsOnly')) {
    /**
     * Asserts that a haystack contains only values of a given type.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertContainsOnly
     */
    function assertContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertContainsOnly(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertContainsOnlyInstancesOf')) {
    /**
     * Asserts that a haystack contains only instances of a given class name.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertContainsOnlyInstancesOf
     */
    function assertContainsOnlyInstancesOf(string $className, iterable $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertContainsOnlyInstancesOf(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotContainsOnly')) {
    /**
     * Asserts that a haystack does not contain only values of a given type.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotContainsOnly
     */
    function assertNotContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotContainsOnly(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertCount')) {
    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param Countable|iterable $haystack
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertCount
     */
    function assertCount(int $expectedCount, $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertCount(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotCount')) {
    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param Countable|iterable $haystack
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotCount
     */
    function assertNotCount(int $expectedCount, $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotCount(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertEquals')) {
    /**
     * Asserts that two variables are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEquals
     */
    function assertEquals($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertEquals(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertEqualsCanonicalizing')) {
    /**
     * Asserts that two variables are equal (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEqualsCanonicalizing
     */
    function assertEqualsCanonicalizing($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertEqualsCanonicalizing(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertEqualsIgnoringCase')) {
    /**
     * Asserts that two variables are equal (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEqualsIgnoringCase
     */
    function assertEqualsIgnoringCase($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertEqualsIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertEqualsWithDelta')) {
    /**
     * Asserts that two variables are equal (with delta).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEqualsWithDelta
     */
    function assertEqualsWithDelta($expected, $actual, float $delta, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertEqualsWithDelta(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotEquals')) {
    /**
     * Asserts that two variables are not equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEquals
     */
    function assertNotEquals($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotEquals(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotEqualsCanonicalizing')) {
    /**
     * Asserts that two variables are not equal (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEqualsCanonicalizing
     */
    function assertNotEqualsCanonicalizing($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotEqualsCanonicalizing(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotEqualsIgnoringCase')) {
    /**
     * Asserts that two variables are not equal (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEqualsIgnoringCase
     */
    function assertNotEqualsIgnoringCase($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotEqualsIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotEqualsWithDelta')) {
    /**
     * Asserts that two variables are not equal (with delta).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEqualsWithDelta
     */
    function assertNotEqualsWithDelta($expected, $actual, float $delta, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotEqualsWithDelta(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertObjectEquals')) {
    /**
     * @throws ExpectationFailedException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertObjectEquals
     */
    function assertObjectEquals(object $expected, object $actual, string $method = 'equals', string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertObjectEquals(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertEmpty')) {
    /**
     * Asserts that a variable is empty.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert empty $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEmpty
     */
    function assertEmpty($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertEmpty(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotEmpty')) {
    /**
     * Asserts that a variable is not empty.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !empty $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotEmpty
     */
    function assertNotEmpty($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotEmpty(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertGreaterThan')) {
    /**
     * Asserts that a value is greater than another value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertGreaterThan
     */
    function assertGreaterThan($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertGreaterThan(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertGreaterThanOrEqual')) {
    /**
     * Asserts that a value is greater than or equal to another value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertGreaterThanOrEqual
     */
    function assertGreaterThanOrEqual($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertGreaterThanOrEqual(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertLessThan')) {
    /**
     * Asserts that a value is smaller than another value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertLessThan
     */
    function assertLessThan($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertLessThan(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertLessThanOrEqual')) {
    /**
     * Asserts that a value is smaller than or equal to another value.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertLessThanOrEqual
     */
    function assertLessThanOrEqual($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertLessThanOrEqual(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileEquals')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileEquals
     */
    function assertFileEquals(string $expected, string $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileEquals(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileEqualsCanonicalizing')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileEqualsCanonicalizing
     */
    function assertFileEqualsCanonicalizing(string $expected, string $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileEqualsCanonicalizing(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileEqualsIgnoringCase')) {
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileEqualsIgnoringCase
     */
    function assertFileEqualsIgnoringCase(string $expected, string $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileEqualsIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileNotEquals')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of
     * another file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotEquals
     */
    function assertFileNotEquals(string $expected, string $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileNotEquals(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileNotEqualsCanonicalizing')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of another
     * file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotEqualsCanonicalizing
     */
    function assertFileNotEqualsCanonicalizing(string $expected, string $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileNotEqualsCanonicalizing(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileNotEqualsIgnoringCase')) {
    /**
     * Asserts that the contents of one file is not equal to the contents of another
     * file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotEqualsIgnoringCase
     */
    function assertFileNotEqualsIgnoringCase(string $expected, string $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileNotEqualsIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringEqualsFile')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEqualsFile
     */
    function assertStringEqualsFile(string $expectedFile, string $actualString, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringEqualsFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringEqualsFileCanonicalizing')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEqualsFileCanonicalizing
     */
    function assertStringEqualsFileCanonicalizing(string $expectedFile, string $actualString, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringEqualsFileCanonicalizing(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringEqualsFileIgnoringCase')) {
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEqualsFileIgnoringCase
     */
    function assertStringEqualsFileIgnoringCase(string $expectedFile, string $actualString, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringEqualsFileIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringNotEqualsFile')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotEqualsFile
     */
    function assertStringNotEqualsFile(string $expectedFile, string $actualString, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringNotEqualsFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringNotEqualsFileCanonicalizing')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file (canonicalizing).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotEqualsFileCanonicalizing
     */
    function assertStringNotEqualsFileCanonicalizing(string $expectedFile, string $actualString, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringNotEqualsFileCanonicalizing(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringNotEqualsFileIgnoringCase')) {
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file (ignoring case).
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotEqualsFileIgnoringCase
     */
    function assertStringNotEqualsFileIgnoringCase(string $expectedFile, string $actualString, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringNotEqualsFileIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsReadable')) {
    /**
     * Asserts that a file/dir is readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsReadable
     */
    function assertIsReadable(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotReadable')) {
    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotReadable
     */
    function assertIsNotReadable(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotIsReadable')) {
    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4062
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotIsReadable
     */
    function assertNotIsReadable(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotIsReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsWritable')) {
    /**
     * Asserts that a file/dir exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsWritable
     */
    function assertIsWritable(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotWritable')) {
    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotWritable
     */
    function assertIsNotWritable(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotIsWritable')) {
    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4065
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotIsWritable
     */
    function assertNotIsWritable(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotIsWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryExists')) {
    /**
     * Asserts that a directory exists.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryExists
     */
    function assertDirectoryExists(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryExists(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryDoesNotExist')) {
    /**
     * Asserts that a directory does not exist.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryDoesNotExist
     */
    function assertDirectoryDoesNotExist(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryDoesNotExist(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryNotExists')) {
    /**
     * Asserts that a directory does not exist.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4068
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryNotExists
     */
    function assertDirectoryNotExists(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryNotExists(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryIsReadable')) {
    /**
     * Asserts that a directory exists and is readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryIsReadable
     */
    function assertDirectoryIsReadable(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryIsReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryIsNotReadable')) {
    /**
     * Asserts that a directory exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryIsNotReadable
     */
    function assertDirectoryIsNotReadable(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryIsNotReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryNotIsReadable')) {
    /**
     * Asserts that a directory exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4071
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryNotIsReadable
     */
    function assertDirectoryNotIsReadable(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryNotIsReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryIsWritable')) {
    /**
     * Asserts that a directory exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryIsWritable
     */
    function assertDirectoryIsWritable(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryIsWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryIsNotWritable')) {
    /**
     * Asserts that a directory exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryIsNotWritable
     */
    function assertDirectoryIsNotWritable(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryIsNotWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDirectoryNotIsWritable')) {
    /**
     * Asserts that a directory exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4074
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDirectoryNotIsWritable
     */
    function assertDirectoryNotIsWritable(string $directory, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDirectoryNotIsWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileExists')) {
    /**
     * Asserts that a file exists.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileExists
     */
    function assertFileExists(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileExists(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileDoesNotExist')) {
    /**
     * Asserts that a file does not exist.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileDoesNotExist
     */
    function assertFileDoesNotExist(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileDoesNotExist(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileNotExists')) {
    /**
     * Asserts that a file does not exist.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4077
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotExists
     */
    function assertFileNotExists(string $filename, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileNotExists(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileIsReadable')) {
    /**
     * Asserts that a file exists and is readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileIsReadable
     */
    function assertFileIsReadable(string $file, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileIsReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileIsNotReadable')) {
    /**
     * Asserts that a file exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileIsNotReadable
     */
    function assertFileIsNotReadable(string $file, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileIsNotReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileNotIsReadable')) {
    /**
     * Asserts that a file exists and is not readable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4080
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotIsReadable
     */
    function assertFileNotIsReadable(string $file, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileNotIsReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileIsWritable')) {
    /**
     * Asserts that a file exists and is writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileIsWritable
     */
    function assertFileIsWritable(string $file, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileIsWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileIsNotWritable')) {
    /**
     * Asserts that a file exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileIsNotWritable
     */
    function assertFileIsNotWritable(string $file, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileIsNotWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFileNotIsWritable')) {
    /**
     * Asserts that a file exists and is not writable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4083
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFileNotIsWritable
     */
    function assertFileNotIsWritable(string $file, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFileNotIsWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertTrue')) {
    /**
     * Asserts that a condition is true.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert true $condition
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertTrue
     */
    function assertTrue($condition, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertTrue(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotTrue')) {
    /**
     * Asserts that a condition is not true.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !true $condition
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotTrue
     */
    function assertNotTrue($condition, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotTrue(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFalse')) {
    /**
     * Asserts that a condition is false.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert false $condition
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFalse
     */
    function assertFalse($condition, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFalse(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotFalse')) {
    /**
     * Asserts that a condition is not false.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !false $condition
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotFalse
     */
    function assertNotFalse($condition, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotFalse(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNull')) {
    /**
     * Asserts that a variable is null.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert null $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNull
     */
    function assertNull($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNull(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotNull')) {
    /**
     * Asserts that a variable is not null.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !null $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotNull
     */
    function assertNotNull($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotNull(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertFinite')) {
    /**
     * Asserts that a variable is finite.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertFinite
     */
    function assertFinite($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertFinite(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertInfinite')) {
    /**
     * Asserts that a variable is infinite.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertInfinite
     */
    function assertInfinite($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertInfinite(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNan')) {
    /**
     * Asserts that a variable is nan.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNan
     */
    function assertNan($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNan(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertClassHasAttribute')) {
    /**
     * Asserts that a class has a specified attribute.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertClassHasAttribute
     */
    function assertClassHasAttribute(string $attributeName, string $className, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertClassHasAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertClassNotHasAttribute')) {
    /**
     * Asserts that a class does not have a specified attribute.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertClassNotHasAttribute
     */
    function assertClassNotHasAttribute(string $attributeName, string $className, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertClassNotHasAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertClassHasStaticAttribute')) {
    /**
     * Asserts that a class has a specified static attribute.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertClassHasStaticAttribute
     */
    function assertClassHasStaticAttribute(string $attributeName, string $className, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertClassHasStaticAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertClassNotHasStaticAttribute')) {
    /**
     * Asserts that a class does not have a specified static attribute.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertClassNotHasStaticAttribute
     */
    function assertClassNotHasStaticAttribute(string $attributeName, string $className, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertClassNotHasStaticAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertObjectHasAttribute')) {
    /**
     * Asserts that an object has a specified attribute.
     *
     * @param object $object
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertObjectHasAttribute
     */
    function assertObjectHasAttribute(string $attributeName, $object, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertObjectHasAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertObjectNotHasAttribute')) {
    /**
     * Asserts that an object does not have a specified attribute.
     *
     * @param object $object
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertObjectNotHasAttribute
     */
    function assertObjectNotHasAttribute(string $attributeName, $object, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertObjectNotHasAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertSame')) {
    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-template ExpectedType
     * @psalm-param ExpectedType $expected
     * @psalm-assert =ExpectedType $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertSame
     */
    function assertSame($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertSame(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotSame')) {
    /**
     * Asserts that two variables do not have the same type and value.
     * Used on objects, it asserts that two variables do not reference
     * the same object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotSame
     */
    function assertNotSame($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotSame(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertInstanceOf')) {
    /**
     * Asserts that a variable is of a given type.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @psalm-template ExpectedType of object
     * @psalm-param class-string<ExpectedType> $expected
     * @psalm-assert =ExpectedType $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertInstanceOf
     */
    function assertInstanceOf(string $expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertInstanceOf(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotInstanceOf')) {
    /**
     * Asserts that a variable is not of a given type.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @psalm-template ExpectedType of object
     * @psalm-param class-string<ExpectedType> $expected
     * @psalm-assert !ExpectedType $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotInstanceOf
     */
    function assertNotInstanceOf(string $expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotInstanceOf(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsArray')) {
    /**
     * Asserts that a variable is of type array.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert array $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsArray
     */
    function assertIsArray($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsArray(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsBool')) {
    /**
     * Asserts that a variable is of type bool.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert bool $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsBool
     */
    function assertIsBool($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsBool(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsFloat')) {
    /**
     * Asserts that a variable is of type float.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert float $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsFloat
     */
    function assertIsFloat($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsFloat(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsInt')) {
    /**
     * Asserts that a variable is of type int.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert int $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsInt
     */
    function assertIsInt($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsInt(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNumeric')) {
    /**
     * Asserts that a variable is of type numeric.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert numeric $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNumeric
     */
    function assertIsNumeric($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNumeric(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsObject')) {
    /**
     * Asserts that a variable is of type object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert object $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsObject
     */
    function assertIsObject($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsObject(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsResource')) {
    /**
     * Asserts that a variable is of type resource.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert resource $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsResource
     */
    function assertIsResource($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsResource(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsClosedResource')) {
    /**
     * Asserts that a variable is of type resource and is closed.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert resource $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsClosedResource
     */
    function assertIsClosedResource($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsClosedResource(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsString')) {
    /**
     * Asserts that a variable is of type string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert string $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsString
     */
    function assertIsString($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsString(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsScalar')) {
    /**
     * Asserts that a variable is of type scalar.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert scalar $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsScalar
     */
    function assertIsScalar($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsScalar(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsCallable')) {
    /**
     * Asserts that a variable is of type callable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert callable $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsCallable
     */
    function assertIsCallable($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsCallable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsIterable')) {
    /**
     * Asserts that a variable is of type iterable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert iterable $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsIterable
     */
    function assertIsIterable($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsIterable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotArray')) {
    /**
     * Asserts that a variable is not of type array.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !array $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotArray
     */
    function assertIsNotArray($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotArray(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotBool')) {
    /**
     * Asserts that a variable is not of type bool.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !bool $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotBool
     */
    function assertIsNotBool($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotBool(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotFloat')) {
    /**
     * Asserts that a variable is not of type float.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !float $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotFloat
     */
    function assertIsNotFloat($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotFloat(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotInt')) {
    /**
     * Asserts that a variable is not of type int.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !int $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotInt
     */
    function assertIsNotInt($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotInt(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotNumeric')) {
    /**
     * Asserts that a variable is not of type numeric.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !numeric $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotNumeric
     */
    function assertIsNotNumeric($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotNumeric(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotObject')) {
    /**
     * Asserts that a variable is not of type object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !object $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotObject
     */
    function assertIsNotObject($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotObject(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotResource')) {
    /**
     * Asserts that a variable is not of type resource.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !resource $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotResource
     */
    function assertIsNotResource($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotResource(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotClosedResource')) {
    /**
     * Asserts that a variable is not of type resource.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !resource $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotClosedResource
     */
    function assertIsNotClosedResource($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotClosedResource(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotString')) {
    /**
     * Asserts that a variable is not of type string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !string $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotString
     */
    function assertIsNotString($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotString(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotScalar')) {
    /**
     * Asserts that a variable is not of type scalar.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !scalar $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotScalar
     */
    function assertIsNotScalar($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotScalar(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotCallable')) {
    /**
     * Asserts that a variable is not of type callable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !callable $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotCallable
     */
    function assertIsNotCallable($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotCallable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertIsNotIterable')) {
    /**
     * Asserts that a variable is not of type iterable.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @psalm-assert !iterable $actual
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertIsNotIterable
     */
    function assertIsNotIterable($actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertIsNotIterable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertMatchesRegularExpression')) {
    /**
     * Asserts that a string matches a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertMatchesRegularExpression
     */
    function assertMatchesRegularExpression(string $pattern, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertMatchesRegularExpression(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertRegExp')) {
    /**
     * Asserts that a string matches a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4086
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertRegExp
     */
    function assertRegExp(string $pattern, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertRegExp(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertDoesNotMatchRegularExpression')) {
    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertDoesNotMatchRegularExpression
     */
    function assertDoesNotMatchRegularExpression(string $pattern, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertDoesNotMatchRegularExpression(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotRegExp')) {
    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4089
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotRegExp
     */
    function assertNotRegExp(string $pattern, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotRegExp(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertSameSize')) {
    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is the same.
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertSameSize
     */
    function assertSameSize($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertSameSize(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertNotSameSize')) {
    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is not the same.
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertNotSameSize
     */
    function assertNotSameSize($expected, $actual, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertNotSameSize(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringMatchesFormat')) {
    /**
     * Asserts that a string matches a given format string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringMatchesFormat
     */
    function assertStringMatchesFormat(string $format, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringMatchesFormat(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringNotMatchesFormat')) {
    /**
     * Asserts that a string does not match a given format string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotMatchesFormat
     */
    function assertStringNotMatchesFormat(string $format, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringNotMatchesFormat(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringMatchesFormatFile')) {
    /**
     * Asserts that a string matches a given format file.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringMatchesFormatFile
     */
    function assertStringMatchesFormatFile(string $formatFile, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringMatchesFormatFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringNotMatchesFormatFile')) {
    /**
     * Asserts that a string does not match a given format string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotMatchesFormatFile
     */
    function assertStringNotMatchesFormatFile(string $formatFile, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringNotMatchesFormatFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringStartsWith')) {
    /**
     * Asserts that a string starts with a given prefix.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringStartsWith
     */
    function assertStringStartsWith(string $prefix, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringStartsWith(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringStartsNotWith')) {
    /**
     * Asserts that a string starts not with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringStartsNotWith
     */
    function assertStringStartsNotWith($prefix, $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringStartsNotWith(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringContainsString')) {
    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringContainsString
     */
    function assertStringContainsString(string $needle, string $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringContainsString(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringContainsStringIgnoringCase')) {
    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringContainsStringIgnoringCase
     */
    function assertStringContainsStringIgnoringCase(string $needle, string $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringContainsStringIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringNotContainsString')) {
    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotContainsString
     */
    function assertStringNotContainsString(string $needle, string $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringNotContainsString(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringNotContainsStringIgnoringCase')) {
    /**
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringNotContainsStringIgnoringCase
     */
    function assertStringNotContainsStringIgnoringCase(string $needle, string $haystack, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringNotContainsStringIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringEndsWith')) {
    /**
     * Asserts that a string ends with a given suffix.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEndsWith
     */
    function assertStringEndsWith(string $suffix, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringEndsWith(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertStringEndsNotWith')) {
    /**
     * Asserts that a string ends not with a given suffix.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertStringEndsNotWith
     */
    function assertStringEndsNotWith(string $suffix, string $string, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertStringEndsNotWith(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertXmlFileEqualsXmlFile')) {
    /**
     * Asserts that two XML files are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlFileEqualsXmlFile
     */
    function assertXmlFileEqualsXmlFile(string $expectedFile, string $actualFile, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertXmlFileEqualsXmlFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertXmlFileNotEqualsXmlFile')) {
    /**
     * Asserts that two XML files are not equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlFileNotEqualsXmlFile
     */
    function assertXmlFileNotEqualsXmlFile(string $expectedFile, string $actualFile, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertXmlFileNotEqualsXmlFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertXmlStringEqualsXmlFile')) {
    /**
     * Asserts that two XML documents are equal.
     *
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlStringEqualsXmlFile
     */
    function assertXmlStringEqualsXmlFile(string $expectedFile, $actualXml, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertXmlStringEqualsXmlFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertXmlStringNotEqualsXmlFile')) {
    /**
     * Asserts that two XML documents are not equal.
     *
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlStringNotEqualsXmlFile
     */
    function assertXmlStringNotEqualsXmlFile(string $expectedFile, $actualXml, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertXmlStringNotEqualsXmlFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertXmlStringEqualsXmlString')) {
    /**
     * Asserts that two XML documents are equal.
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlStringEqualsXmlString
     */
    function assertXmlStringEqualsXmlString($expectedXml, $actualXml, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertXmlStringEqualsXmlString(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertXmlStringNotEqualsXmlString')) {
    /**
     * Asserts that two XML documents are not equal.
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Util\Xml\Exception
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertXmlStringNotEqualsXmlString
     */
    function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertXmlStringNotEqualsXmlString(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertEqualXMLStructure')) {
    /**
     * Asserts that a hierarchy of DOMElements matches.
     *
     * @throws AssertionFailedError
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @codeCoverageIgnore
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/4091
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertEqualXMLStructure
     */
    function assertEqualXMLStructure(\DOMElement $expectedElement, \DOMElement $actualElement, bool $checkAttributes = \false, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertEqualXMLStructure(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertThat')) {
    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertThat
     */
    function assertThat($value, \ECSPrefix20210804\PHPUnit\Framework\Constraint\Constraint $constraint, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertThat(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertJson')) {
    /**
     * Asserts that a string is a valid JSON string.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJson
     */
    function assertJson(string $actualJson, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertJson(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertJsonStringEqualsJsonString')) {
    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonStringEqualsJsonString
     */
    function assertJsonStringEqualsJsonString(string $expectedJson, string $actualJson, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertJsonStringEqualsJsonString(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertJsonStringNotEqualsJsonString')) {
    /**
     * Asserts that two given JSON encoded objects or arrays are not equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonStringNotEqualsJsonString
     */
    function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertJsonStringNotEqualsJsonString(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertJsonStringEqualsJsonFile')) {
    /**
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonStringEqualsJsonFile
     */
    function assertJsonStringEqualsJsonFile(string $expectedFile, string $actualJson, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertJsonStringEqualsJsonFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertJsonStringNotEqualsJsonFile')) {
    /**
     * Asserts that the generated JSON encoded object and the content of the given file are not equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonStringNotEqualsJsonFile
     */
    function assertJsonStringNotEqualsJsonFile(string $expectedFile, string $actualJson, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertJsonStringNotEqualsJsonFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertJsonFileEqualsJsonFile')) {
    /**
     * Asserts that two JSON files are equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonFileEqualsJsonFile
     */
    function assertJsonFileEqualsJsonFile(string $expectedFile, string $actualFile, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertJsonFileEqualsJsonFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\assertJsonFileNotEqualsJsonFile')) {
    /**
     * Asserts that two JSON files are not equal.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
     *
     * @see Assert::assertJsonFileNotEqualsJsonFile
     */
    function assertJsonFileNotEqualsJsonFile(string $expectedFile, string $actualFile, string $message = '') : void
    {
        \ECSPrefix20210804\PHPUnit\Framework\Assert::assertJsonFileNotEqualsJsonFile(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\logicalAnd')) {
    function logicalAnd() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalAnd
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::logicalAnd(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\logicalOr')) {
    function logicalOr() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalOr
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::logicalOr(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\logicalNot')) {
    function logicalNot(\ECSPrefix20210804\PHPUnit\Framework\Constraint\Constraint $constraint) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalNot
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::logicalNot(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\logicalXor')) {
    function logicalXor() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalXor
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::logicalXor(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\anything')) {
    function anything() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsAnything
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::anything(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isTrue')) {
    function isTrue() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsTrue
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isTrue(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\callback')) {
    function callback(callable $callback) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\Callback
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::callback(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isFalse')) {
    function isFalse() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsFalse
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isFalse(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isJson')) {
    function isJson() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsJson
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isJson(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isNull')) {
    function isNull() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsNull
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isNull(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isFinite')) {
    function isFinite() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsFinite
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isFinite(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isInfinite')) {
    function isInfinite() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsInfinite
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isInfinite(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isNan')) {
    function isNan() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsNan
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isNan(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\containsEqual')) {
    function containsEqual($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\TraversableContainsEqual
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::containsEqual(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\containsIdentical')) {
    function containsIdentical($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\TraversableContainsIdentical
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::containsIdentical(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\containsOnly')) {
    function containsOnly(string $type) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\TraversableContainsOnly
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::containsOnly(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\containsOnlyInstancesOf')) {
    function containsOnlyInstancesOf(string $className) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\TraversableContainsOnly
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::containsOnlyInstancesOf(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\arrayHasKey')) {
    function arrayHasKey($key) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\ArrayHasKey
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::arrayHasKey(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\equalTo')) {
    function equalTo($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqual
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::equalTo(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\equalToCanonicalizing')) {
    function equalToCanonicalizing($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqualCanonicalizing
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::equalToCanonicalizing(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\equalToIgnoringCase')) {
    function equalToIgnoringCase($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqualIgnoringCase
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::equalToIgnoringCase(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\equalToWithDelta')) {
    function equalToWithDelta($value, float $delta) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqualWithDelta
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::equalToWithDelta(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isEmpty')) {
    function isEmpty() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEmpty
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isEmpty(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isWritable')) {
    function isWritable() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsWritable
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isWritable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isReadable')) {
    function isReadable() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsReadable
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isReadable(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\directoryExists')) {
    function directoryExists() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\DirectoryExists
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::directoryExists(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\fileExists')) {
    function fileExists() : \ECSPrefix20210804\PHPUnit\Framework\Constraint\FileExists
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::fileExists(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\greaterThan')) {
    function greaterThan($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\GreaterThan
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::greaterThan(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\greaterThanOrEqual')) {
    function greaterThanOrEqual($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalOr
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::greaterThanOrEqual(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\classHasAttribute')) {
    function classHasAttribute(string $attributeName) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\ClassHasAttribute
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::classHasAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\classHasStaticAttribute')) {
    function classHasStaticAttribute(string $attributeName) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\ClassHasStaticAttribute
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::classHasStaticAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\objectHasAttribute')) {
    function objectHasAttribute($attributeName) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\ObjectHasAttribute
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::objectHasAttribute(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\identicalTo')) {
    function identicalTo($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsIdentical
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::identicalTo(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isInstanceOf')) {
    function isInstanceOf(string $className) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsInstanceOf
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isInstanceOf(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\isType')) {
    function isType(string $type) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsType
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::isType(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\lessThan')) {
    function lessThan($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\LessThan
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::lessThan(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\lessThanOrEqual')) {
    function lessThanOrEqual($value) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\LogicalOr
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::lessThanOrEqual(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\matchesRegularExpression')) {
    function matchesRegularExpression(string $pattern) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\RegularExpression
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::matchesRegularExpression(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\matches')) {
    function matches(string $string) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\StringMatchesFormatDescription
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::matches(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\stringStartsWith')) {
    function stringStartsWith($prefix) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\StringStartsWith
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::stringStartsWith(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\stringContains')) {
    function stringContains(string $string, bool $case = \true) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\StringContains
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::stringContains(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\stringEndsWith')) {
    function stringEndsWith(string $suffix) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\StringEndsWith
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::stringEndsWith(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\countOf')) {
    function countOf(int $count) : \ECSPrefix20210804\PHPUnit\Framework\Constraint\Count
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::countOf(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\objectEquals')) {
    function objectEquals(object $object, string $method = 'equals') : \ECSPrefix20210804\PHPUnit\Framework\Constraint\ObjectEquals
    {
        return \ECSPrefix20210804\PHPUnit\Framework\Assert::objectEquals(...\func_get_args());
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\any')) {
    /**
     * Returns a matcher that matches when the method is executed
     * zero or more times.
     */
    function any() : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\AnyInvokedCount
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\AnyInvokedCount();
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\never')) {
    /**
     * Returns a matcher that matches when the method is never executed.
     */
    function never() : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedCount
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedCount(0);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\atLeast')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at least N times.
     */
    function atLeast(int $requiredInvocations) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtLeastCount
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtLeastCount($requiredInvocations);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\atLeastOnce')) {
    /**
     * Returns a matcher that matches when the method is executed at least once.
     */
    function atLeastOnce() : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce();
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\once')) {
    /**
     * Returns a matcher that matches when the method is executed exactly once.
     */
    function once() : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedCount
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedCount(1);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\exactly')) {
    /**
     * Returns a matcher that matches when the method is executed
     * exactly $count times.
     */
    function exactly(int $count) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedCount
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedCount($count);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\atMost')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at most N times.
     */
    function atMost(int $allowedInvocations) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtMostCount
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtMostCount($allowedInvocations);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\at')) {
    /**
     * Returns a matcher that matches when the method is executed
     * at the given index.
     */
    function at(int $index) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtIndex
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\InvokedAtIndex($index);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\returnValue')) {
    function returnValue($value) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnStub
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnStub($value);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\returnValueMap')) {
    function returnValueMap(array $valueMap) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnValueMap
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnValueMap($valueMap);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\returnArgument')) {
    function returnArgument(int $argumentIndex) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnArgument
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnArgument($argumentIndex);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\returnCallback')) {
    function returnCallback($callback) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnCallback
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnCallback($callback);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\returnSelf')) {
    /**
     * Returns the current object.
     *
     * This method is useful when mocking a fluent interface.
     */
    function returnSelf() : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnSelf
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ReturnSelf();
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\throwException')) {
    function throwException(\Throwable $exception) : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\Exception
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\Exception($exception);
    }
}
if (!\function_exists('ECSPrefix20210804\\PHPUnit\\Framework\\onConsecutiveCalls')) {
    function onConsecutiveCalls() : \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls
    {
        $args = \func_get_args();
        return new \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls($args);
    }
}
