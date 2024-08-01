<?php

declare (strict_types=1);
namespace ECSPrefix202408\Composer\Pcre\PHPStan;

use ECSPrefix202408\Composer\Pcre\Preg;
use ECSPrefix202408\PhpParser\Node\Expr\StaticCall;
use ECSPrefix202408\PHPStan\Analyser\Scope;
use ECSPrefix202408\PHPStan\Reflection\MethodReflection;
use ECSPrefix202408\PHPStan\Reflection\ParameterReflection;
use ECSPrefix202408\PHPStan\TrinaryLogic;
use ECSPrefix202408\PHPStan\Type\Php\RegexArrayShapeMatcher;
use ECSPrefix202408\PHPStan\Type\StaticMethodParameterOutTypeExtension;
use ECSPrefix202408\PHPStan\Type\Type;
final class PregMatchParameterOutTypeExtension implements StaticMethodParameterOutTypeExtension
{
    /**
     * @var RegexArrayShapeMatcher
     */
    private $regexShapeMatcher;
    public function __construct(RegexArrayShapeMatcher $regexShapeMatcher)
    {
        $this->regexShapeMatcher = $regexShapeMatcher;
    }
    public function isStaticMethodSupported(MethodReflection $methodReflection, ParameterReflection $parameter) : bool
    {
        return $methodReflection->getDeclaringClass()->getName() === Preg::class && \in_array($methodReflection->getName(), ['match', 'isMatch', 'matchStrictGroups', 'isMatchStrictGroups'], \true) && $parameter->getName() === 'matches';
    }
    public function getParameterOutTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, ParameterReflection $parameter, Scope $scope) : ?Type
    {
        $args = $methodCall->getArgs();
        $patternArg = $args[0] ?? null;
        $matchesArg = $args[2] ?? null;
        $flagsArg = $args[3] ?? null;
        if ($patternArg === null || $matchesArg === null) {
            return null;
        }
        $flagsType = PregMatchFlags::getType($flagsArg, $scope);
        if ($flagsType === null) {
            return null;
        }
        return $this->regexShapeMatcher->matchExpr($patternArg->value, $flagsType, TrinaryLogic::createMaybe(), $scope);
    }
}
