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

use function is_string;
use ECSPrefix20210804\PHPUnit\Framework\Constraint\Constraint;
use ECSPrefix20210804\PHPUnit\Framework\InvalidArgumentException;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\MethodNameConstraint;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class MethodName
{
    /**
     * @var Constraint
     */
    private $constraint;
    /**
     * @param Constraint|string $constraint
     *
     * @throws InvalidArgumentException
     */
    public function __construct($constraint)
    {
        if (\is_string($constraint)) {
            $constraint = new \ECSPrefix20210804\PHPUnit\Framework\MockObject\MethodNameConstraint($constraint);
        }
        if (!$constraint instanceof \ECSPrefix20210804\PHPUnit\Framework\Constraint\Constraint) {
            throw \ECSPrefix20210804\PHPUnit\Framework\InvalidArgumentException::create(1, 'PHPUnit\\Framework\\Constraint\\Constraint object or string');
        }
        $this->constraint = $constraint;
    }
    public function toString() : string
    {
        return 'method name ' . $this->constraint->toString();
    }
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function matches(\ECSPrefix20210804\PHPUnit\Framework\MockObject\Invocation $invocation) : bool
    {
        return $this->matchesName($invocation->getMethodName());
    }
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function matchesName(string $methodName) : bool
    {
        return (bool) $this->constraint->evaluate($methodName, '', \true);
    }
}
