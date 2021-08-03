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
namespace ECSPrefix20210803\PHPUnit\Framework\MockObject;

use function assert;
use function implode;
use function sprintf;
use ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\AnyParameters;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\InvokedCount;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\MethodName;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\ParametersRule;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub;
use ECSPrefix20210803\PHPUnit\Framework\TestFailure;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Matcher
{
    /**
     * @var InvocationOrder
     */
    private $invocationRule;
    /**
     * @var mixed
     */
    private $afterMatchBuilderId;
    /**
     * @var bool
     */
    private $afterMatchBuilderIsInvoked = \false;
    /**
     * @var MethodName
     */
    private $methodNameRule;
    /**
     * @var ParametersRule
     */
    private $parametersRule;
    /**
     * @var Stub
     */
    private $stub;
    public function __construct(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\InvocationOrder $rule)
    {
        $this->invocationRule = $rule;
    }
    public function hasMatchers() : bool
    {
        return !$this->invocationRule instanceof \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;
    }
    public function hasMethodNameRule() : bool
    {
        return $this->methodNameRule !== null;
    }
    public function getMethodNameRule() : \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\MethodName
    {
        return $this->methodNameRule;
    }
    public function setMethodNameRule(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\MethodName $rule) : void
    {
        $this->methodNameRule = $rule;
    }
    public function hasParametersRule() : bool
    {
        return $this->parametersRule !== null;
    }
    public function setParametersRule(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\ParametersRule $rule) : void
    {
        $this->parametersRule = $rule;
    }
    public function setStub(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub $stub) : void
    {
        $this->stub = $stub;
    }
    public function setAfterMatchBuilderId(string $id) : void
    {
        $this->afterMatchBuilderId = $id;
    }
    /**
     * @throws ExpectationFailedException
     * @throws MatchBuilderNotFoundException
     * @throws MethodNameNotConfiguredException
     * @throws RuntimeException
     */
    public function invoked(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        if ($this->methodNameRule === null) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodNameNotConfiguredException();
        }
        if ($this->afterMatchBuilderId !== null) {
            $matcher = $invocation->getObject()->__phpunit_getInvocationHandler()->lookupMatcher($this->afterMatchBuilderId);
            if (!$matcher) {
                throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MatchBuilderNotFoundException($this->afterMatchBuilderId);
            }
            \assert($matcher instanceof self);
            if ($matcher->invocationRule->hasBeenInvoked()) {
                $this->afterMatchBuilderIsInvoked = \true;
            }
        }
        $this->invocationRule->invoked($invocation);
        try {
            if ($this->parametersRule !== null) {
                $this->parametersRule->apply($invocation);
            }
        } catch (\ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException $e) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException(\sprintf("Expectation failed for %s when %s\n%s", $this->methodNameRule->toString(), $this->invocationRule->toString(), $e->getMessage()), $e->getComparisonFailure());
        }
        if ($this->stub) {
            return $this->stub->invoke($invocation);
        }
        return $invocation->generateReturnValue();
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws MatchBuilderNotFoundException
     * @throws MethodNameNotConfiguredException
     * @throws RuntimeException
     */
    public function matches(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation $invocation) : bool
    {
        if ($this->afterMatchBuilderId !== null) {
            $matcher = $invocation->getObject()->__phpunit_getInvocationHandler()->lookupMatcher($this->afterMatchBuilderId);
            if (!$matcher) {
                throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MatchBuilderNotFoundException($this->afterMatchBuilderId);
            }
            \assert($matcher instanceof self);
            if (!$matcher->invocationRule->hasBeenInvoked()) {
                return \false;
            }
        }
        if ($this->methodNameRule === null) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodNameNotConfiguredException();
        }
        if (!$this->invocationRule->matches($invocation)) {
            return \false;
        }
        try {
            if (!$this->methodNameRule->matches($invocation)) {
                return \false;
            }
        } catch (\ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException $e) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException(\sprintf("Expectation failed for %s when %s\n%s", $this->methodNameRule->toString(), $this->invocationRule->toString(), $e->getMessage()), $e->getComparisonFailure());
        }
        return \true;
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws MethodNameNotConfiguredException
     */
    public function verify() : void
    {
        if ($this->methodNameRule === null) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodNameNotConfiguredException();
        }
        try {
            $this->invocationRule->verify();
            if ($this->parametersRule === null) {
                $this->parametersRule = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\AnyParameters();
            }
            $invocationIsAny = $this->invocationRule instanceof \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;
            $invocationIsNever = $this->invocationRule instanceof \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\InvokedCount && $this->invocationRule->isNever();
            if (!$invocationIsAny && !$invocationIsNever) {
                $this->parametersRule->verify();
            }
        } catch (\ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException $e) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException(\sprintf("Expectation failed for %s when %s.\n%s", $this->methodNameRule->toString(), $this->invocationRule->toString(), \ECSPrefix20210803\PHPUnit\Framework\TestFailure::exceptionToString($e)));
        }
    }
    public function toString() : string
    {
        $list = [];
        if ($this->invocationRule !== null) {
            $list[] = $this->invocationRule->toString();
        }
        if ($this->methodNameRule !== null) {
            $list[] = 'where ' . $this->methodNameRule->toString();
        }
        if ($this->parametersRule !== null) {
            $list[] = 'and ' . $this->parametersRule->toString();
        }
        if ($this->afterMatchBuilderId !== null) {
            $list[] = 'after ' . $this->afterMatchBuilderId;
        }
        if ($this->stub !== null) {
            $list[] = 'will ' . $this->stub->toString();
        }
        return \implode(' ', $list);
    }
}
