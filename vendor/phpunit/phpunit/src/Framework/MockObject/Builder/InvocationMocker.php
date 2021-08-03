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
namespace ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder;

use function array_map;
use function array_merge;
use function count;
use function in_array;
use function is_string;
use function strtolower;
use ECSPrefix20210803\PHPUnit\Framework\Constraint\Constraint;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\ConfigurableMethod;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\IncompatibleReturnValueException;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\InvocationHandler;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Matcher;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\MatcherAlreadyRegisteredException;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodNameNotConfiguredException;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodParametersAlreadyConfiguredException;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Exception;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnArgument;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnCallback;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnReference;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnSelf;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnStub;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnValueMap;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub;
use Throwable;
/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class InvocationMocker implements \ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder\InvocationStubber, \ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder\MethodNameMatch
{
    /**
     * @var InvocationHandler
     */
    private $invocationHandler;
    /**
     * @var Matcher
     */
    private $matcher;
    /**
     * @var ConfigurableMethod[]
     */
    private $configurableMethods;
    public function __construct(\ECSPrefix20210803\PHPUnit\Framework\MockObject\InvocationHandler $handler, \ECSPrefix20210803\PHPUnit\Framework\MockObject\Matcher $matcher, \ECSPrefix20210803\PHPUnit\Framework\MockObject\ConfigurableMethod ...$configurableMethods)
    {
        $this->invocationHandler = $handler;
        $this->matcher = $matcher;
        $this->configurableMethods = $configurableMethods;
    }
    /**
     * @throws MatcherAlreadyRegisteredException
     *
     * @return $this
     */
    public function id($id) : self
    {
        $this->invocationHandler->registerMatcher($id, $this->matcher);
        return $this;
    }
    /**
     * @return $this
     */
    public function will(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub $stub) : \ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder\Identity
    {
        $this->matcher->setStub($stub);
        return $this;
    }
    /**
     * @param mixed   $value
     * @param mixed[] $nextValues
     *
     * @throws IncompatibleReturnValueException
     */
    public function willReturn($value, ...$nextValues) : self
    {
        if (\count($nextValues) === 0) {
            $this->ensureTypeOfReturnValues([$value]);
            $stub = $value instanceof \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub ? $value : new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnStub($value);
        } else {
            $values = \array_merge([$value], $nextValues);
            $this->ensureTypeOfReturnValues($values);
            $stub = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls($values);
        }
        return $this->will($stub);
    }
    public function willReturnReference(&$reference) : self
    {
        $stub = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnReference($reference);
        return $this->will($stub);
    }
    public function willReturnMap(array $valueMap) : self
    {
        $stub = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnValueMap($valueMap);
        return $this->will($stub);
    }
    public function willReturnArgument($argumentIndex) : self
    {
        $stub = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnArgument($argumentIndex);
        return $this->will($stub);
    }
    public function willReturnCallback($callback) : self
    {
        $stub = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnCallback($callback);
        return $this->will($stub);
    }
    public function willReturnSelf() : self
    {
        $stub = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ReturnSelf();
        return $this->will($stub);
    }
    public function willReturnOnConsecutiveCalls(...$values) : self
    {
        $stub = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls($values);
        return $this->will($stub);
    }
    public function willThrowException(\Throwable $exception) : self
    {
        $stub = new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Exception($exception);
        return $this->will($stub);
    }
    /**
     * @return $this
     */
    public function after($id) : self
    {
        $this->matcher->setAfterMatchBuilderId($id);
        return $this;
    }
    /**
     * @param mixed[] $arguments
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     *
     * @return $this
     */
    public function with(...$arguments) : self
    {
        $this->ensureParametersCanBeConfigured();
        $this->matcher->setParametersRule(new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\Parameters($arguments));
        return $this;
    }
    /**
     * @param array ...$arguments
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     *
     * @return $this
     */
    public function withConsecutive(...$arguments) : self
    {
        $this->ensureParametersCanBeConfigured();
        $this->matcher->setParametersRule(new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\ConsecutiveParameters($arguments));
        return $this;
    }
    /**
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     *
     * @return $this
     */
    public function withAnyParameters() : self
    {
        $this->ensureParametersCanBeConfigured();
        $this->matcher->setParametersRule(new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\AnyParameters());
        return $this;
    }
    /**
     * @param Constraint|string $constraint
     *
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws MethodCannotBeConfiguredException
     * @throws MethodNameAlreadyConfiguredException
     *
     * @return $this
     */
    public function method($constraint) : self
    {
        if ($this->matcher->hasMethodNameRule()) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException();
        }
        $configurableMethodNames = \array_map(static function (\ECSPrefix20210803\PHPUnit\Framework\MockObject\ConfigurableMethod $configurable) {
            return \strtolower($configurable->getName());
        }, $this->configurableMethods);
        if (\is_string($constraint) && !\in_array(\strtolower($constraint), $configurableMethodNames, \true)) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException($constraint);
        }
        $this->matcher->setMethodNameRule(new \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\MethodName($constraint));
        return $this;
    }
    /**
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     */
    private function ensureParametersCanBeConfigured() : void
    {
        if (!$this->matcher->hasMethodNameRule()) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodNameNotConfiguredException();
        }
        if ($this->matcher->hasParametersRule()) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\MethodParametersAlreadyConfiguredException();
        }
    }
    private function getConfiguredMethod() : ?\ECSPrefix20210803\PHPUnit\Framework\MockObject\ConfigurableMethod
    {
        $configuredMethod = null;
        foreach ($this->configurableMethods as $configurableMethod) {
            if ($this->matcher->getMethodNameRule()->matchesName($configurableMethod->getName())) {
                if ($configuredMethod !== null) {
                    return null;
                }
                $configuredMethod = $configurableMethod;
            }
        }
        return $configuredMethod;
    }
    /**
     * @throws IncompatibleReturnValueException
     */
    private function ensureTypeOfReturnValues(array $values) : void
    {
        $configuredMethod = $this->getConfiguredMethod();
        if ($configuredMethod === null) {
            return;
        }
        foreach ($values as $value) {
            if (!$configuredMethod->mayReturn($value)) {
                throw new \ECSPrefix20210803\PHPUnit\Framework\MockObject\IncompatibleReturnValueException($configuredMethod, $value);
            }
        }
    }
}
