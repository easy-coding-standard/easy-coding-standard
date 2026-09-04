<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic;

use ECSPrefix202609\PhpParser\Node\Expr;
use ECSPrefix202609\PhpParser\Node\Expr\MethodCall;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Type\ThisType;
use ECSPrefix202609\PHPStan\Type\TypeCombinator;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ValueObject\MethodCallReference;
final class ClassMethodCallReferenceResolver
{
    /**
     * @return MethodCallReference[]
     */
    public function resolve(MethodCall $methodCall, Scope $scope): array
    {
        if ($methodCall->name instanceof Expr) {
            return [];
        }
        $callerType = $scope->getType($methodCall->var);
        // remove optional nullable type
        if (TypeCombinator::containsNull($callerType)) {
            $callerType = TypeCombinator::removeNull($callerType);
        }
        // unwrap this type, as method is used
        $isLocal = \false;
        if ($callerType instanceof ThisType) {
            $callerType = $callerType->getStaticObjectType();
            $isLocal = \true;
        }
        $methodCallReferences = [];
        foreach ($callerType->getReferencedClasses() as $referencedClass) {
            $methodCallReferences[] = new MethodCallReference($referencedClass, $methodCall->name->toString(), $isLocal);
        }
        return $methodCallReferences;
    }
}
