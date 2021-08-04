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
namespace ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule;

use function count;
use function gettype;
use function is_iterable;
use function sprintf;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\Constraint;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqual;
use ECSPrefix20210804\PHPUnit\Framework\ExpectationFailedException;
use ECSPrefix20210804\PHPUnit\Framework\InvalidParameterGroupException;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ConsecutiveParameters implements \ECSPrefix20210804\PHPUnit\Framework\MockObject\Rule\ParametersRule
{
    /**
     * @var array
     */
    private $parameterGroups = [];
    /**
     * @var array
     */
    private $invocations = [];
    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct(array $parameterGroups)
    {
        foreach ($parameterGroups as $index => $parameters) {
            if (!\is_iterable($parameters)) {
                throw new \ECSPrefix20210804\PHPUnit\Framework\InvalidParameterGroupException(\sprintf('Parameter group #%d must be an array or Traversable, got %s', $index, \gettype($parameters)));
            }
            foreach ($parameters as $parameter) {
                if (!$parameter instanceof \ECSPrefix20210804\PHPUnit\Framework\Constraint\Constraint) {
                    $parameter = new \ECSPrefix20210804\PHPUnit\Framework\Constraint\IsEqual($parameter);
                }
                $this->parameterGroups[$index][] = $parameter;
            }
        }
    }
    public function toString() : string
    {
        return 'with consecutive parameters';
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function apply(\ECSPrefix20210804\PHPUnit\Framework\MockObject\Invocation $invocation) : void
    {
        $this->invocations[] = $invocation;
        $callIndex = \count($this->invocations) - 1;
        $this->verifyInvocation($invocation, $callIndex);
    }
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function verify() : void
    {
        foreach ($this->invocations as $callIndex => $invocation) {
            $this->verifyInvocation($invocation, $callIndex);
        }
    }
    /**
     * Verify a single invocation.
     *
     * @param int $callIndex
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    private function verifyInvocation(\ECSPrefix20210804\PHPUnit\Framework\MockObject\Invocation $invocation, $callIndex) : void
    {
        if (!isset($this->parameterGroups[$callIndex])) {
            // no parameter assertion for this call index
            return;
        }
        $parameters = $this->parameterGroups[$callIndex];
        if (\count($invocation->getParameters()) < \count($parameters)) {
            throw new \ECSPrefix20210804\PHPUnit\Framework\ExpectationFailedException(\sprintf('Parameter count for invocation %s is too low.', $invocation->toString()));
        }
        foreach ($parameters as $i => $parameter) {
            $parameter->evaluate($invocation->getParameters()[$i], \sprintf('Parameter %s for invocation #%d %s does not match expected ' . 'value.', $i, $callIndex, $invocation->toString()));
        }
    }
}
