<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenAnalyzer;

use ECSPrefix202208\Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
final class DoctrineAnnotationNameResolver
{
    /**
     * @param Tokens<Token> $tokens
     * @param NamespaceUseAnalysis[] $namespaceUseAnalyses
     */
    public function resolveName(Tokens $tokens, int $index, array $namespaceUseAnalyses) : ?string
    {
        $openParenthesisPosition = $this->getNextOpenParenthesisFromTokens($tokens, $index);
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
    /**
     * @param Tokens<Token> $tokens
     */
    private function getNextOpenParenthesisFromTokens(Tokens $tokens, int $index) : ?int
    {
        $openParenthesisPosition = $tokens->getNextMeaningfulToken($index);
        if ($openParenthesisPosition === null) {
            return null;
        }
        /** @var Token $nextOpenParenthesis */
        $nextOpenParenthesis = $tokens[$openParenthesisPosition];
        if ($nextOpenParenthesis->isType(DocLexer::T_OPEN_PARENTHESIS)) {
            return $openParenthesisPosition;
        }
        return $this->getNextOpenParenthesisFromTokens($tokens, $openParenthesisPosition);
    }
}
