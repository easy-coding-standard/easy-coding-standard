<?php

declare (strict_types=1);
namespace ECSPrefix202408\Composer\Pcre\PHPStan;

use ECSPrefix202408\Composer\Pcre\Preg;
use ECSPrefix202408\PhpParser\Node\Expr\StaticCall;
use ECSPrefix202408\PHPStan\Analyser\Scope;
use ECSPrefix202408\PHPStan\Analyser\SpecifiedTypes;
use ECSPrefix202408\PHPStan\Analyser\TypeSpecifier;
use ECSPrefix202408\PHPStan\Analyser\TypeSpecifierAwareExtension;
use ECSPrefix202408\PHPStan\Analyser\TypeSpecifierContext;
use ECSPrefix202408\PHPStan\Reflection\MethodReflection;
use ECSPrefix202408\PHPStan\TrinaryLogic;
use ECSPrefix202408\PHPStan\Type\Constant\ConstantArrayType;
use ECSPrefix202408\PHPStan\Type\Php\RegexArrayShapeMatcher;
use ECSPrefix202408\PHPStan\Type\StaticMethodTypeSpecifyingExtension;
use ECSPrefix202408\PHPStan\Type\TypeCombinator;
use ECSPrefix202408\PHPStan\Type\Type;
final class PregMatchTypeSpecifyingExtension implements StaticMethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    /**
     * @var TypeSpecifier
     */
    private $typeSpecifier;
    /**
     * @var RegexArrayShapeMatcher
     */
    private $regexShapeMatcher;
    public function __construct(RegexArrayShapeMatcher $regexShapeMatcher)
    {
        $this->regexShapeMatcher = $regexShapeMatcher;
    }
    public function setTypeSpecifier(TypeSpecifier $typeSpecifier) : void
    {
        $this->typeSpecifier = $typeSpecifier;
    }
    public function getClass() : string
    {
        return Preg::class;
    }
    public function isStaticMethodSupported(MethodReflection $methodReflection, StaticCall $node, TypeSpecifierContext $context) : bool
    {
        return \in_array($methodReflection->getName(), ['match', 'isMatch', 'matchStrictGroups', 'isMatchStrictGroups'], \true) && !$context->null();
    }
    public function specifyTypes(MethodReflection $methodReflection, StaticCall $node, Scope $scope, TypeSpecifierContext $context) : SpecifiedTypes
    {
        $args = $node->getArgs();
        $patternArg = $args[0] ?? null;
        $matchesArg = $args[2] ?? null;
        $flagsArg = $args[3] ?? null;
        if ($patternArg === null || $matchesArg === null) {
            return new SpecifiedTypes();
        }
        $flagsType = PregMatchFlags::getType($flagsArg, $scope);
        if ($flagsType === null) {
            return new SpecifiedTypes();
        }
        $matchedType = $this->regexShapeMatcher->matchExpr($patternArg->value, $flagsType, TrinaryLogic::createFromBoolean($context->true()), $scope);
        if ($matchedType === null) {
            return new SpecifiedTypes();
        }
        if (\in_array($methodReflection->getName(), ['matchStrictGroups', 'isMatchStrictGroups'], \true) && \count($matchedType->getConstantArrays()) === 1) {
            $matchedType = $matchedType->getConstantArrays()[0];
            $matchedType = new ConstantArrayType($matchedType->getKeyTypes(), \array_map(static function (Type $valueType) : Type {
                return TypeCombinator::removeNull($valueType);
            }, $matchedType->getValueTypes()), $matchedType->getNextAutoIndexes(), [], $matchedType->isList());
        }
        $overwrite = \false;
        if ($context->false()) {
            $overwrite = \true;
            $context = $context->negate();
        }
        return $this->typeSpecifier->create($matchesArg->value, $matchedType, $context, $overwrite, $scope, $node);
    }
}
