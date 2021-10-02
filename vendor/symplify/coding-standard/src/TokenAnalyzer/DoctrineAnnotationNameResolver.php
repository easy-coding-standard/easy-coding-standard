<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenAnalyzer;

use ECSPrefix20211002\Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
final class DoctrineAnnotationNameResolver
{
    /**
     * @param Tokens<Token> $tokens
     * @param NamespaceUseAnalysis[] $namespaceUseAnalyses
     */
    public function resolveName(\PhpCsFixer\Doctrine\Annotation\Tokens $tokens, int $index, array $namespaceUseAnalyses) : ?string
    {
        $openParenthesisPosition = $tokens->getNextTokenOfType(\ECSPrefix20211002\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS, $index);
        if ($openParenthesisPosition === null) {
            return null;
        }
        $annotationShortName = '';
        for ($i = $index + 1; $i < $openParenthesisPosition; ++$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];
            $annotationShortName .= $currentToken->getContent();
        }
        if ($annotationShortName === '') {
            return null;
        }
        foreach ($namespaceUseAnalyses as $namespaceUseAnalysis) {
            if ($namespaceUseAnalysis->getShortName() === $annotationShortName) {
                return $namespaceUseAnalysis->getFullName();
            }
        }
        return $annotationShortName;
    }
}
