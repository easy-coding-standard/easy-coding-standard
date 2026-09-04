<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic;

use ECSPrefix202609\PhpParser\Comment\Doc;
use ECSPrefix202609\PhpParser\Node\Stmt;
use ECSPrefix202609\PHPStan\PhpDoc\ResolvedPhpDocBlock;
use ECSPrefix202609\PHPStan\Reflection\ClassReflection;
final class ApiDocStmtAnalyzer
{
    public function isApiDoc(Stmt $stmt, ClassReflection $classReflection): bool
    {
        if ($classReflection->getResolvedPhpDoc() instanceof ResolvedPhpDocBlock) {
            $resolvedPhpDoc = $classReflection->getResolvedPhpDoc();
            if (strpos($resolvedPhpDoc->getPhpDocString(), '@api') !== \false) {
                return \true;
            }
        }
        $docComment = $stmt->getDocComment();
        if (!$docComment instanceof Doc) {
            return \false;
        }
        return $this->isApiDocComment($docComment->getText());
    }
    public function isApiDocComment(string $docComment): bool
    {
        return strpos($docComment, '@api') !== \false;
    }
}
