<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenAnalyzer;

use ECSPrefix20210710\Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens as DoctrineAnnotationTokens;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * Copied from \PhpCsFixer\AbstractDoctrineAnnotationFixer::nextElementAcceptsDoctrineAnnotations() so it can be used as
 * a normal service
 */
final class DoctrineAnnotationElementAnalyzer
{
    /**
     * We look for "(@SomeAnnotation"
     *
     * @param DoctrineAnnotationTokens<Token> $doctrineAnnotationTokens
     */
    public function isOpeningBracketFollowedByAnnotation(\PhpCsFixer\Doctrine\Annotation\Token $token, \PhpCsFixer\Doctrine\Annotation\Tokens $doctrineAnnotationTokens, int $braceIndex) : bool
    {
        // should be "("
        $isNextOpenParenthesis = $token->isType(\ECSPrefix20210710\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS);
        if (!$isNextOpenParenthesis) {
            return \false;
        }
        $nextTokenIndex = $doctrineAnnotationTokens->getNextMeaningfulToken($braceIndex);
        if ($nextTokenIndex === null) {
            return \false;
        }
        /** @var Token $nextToken */
        $nextToken = $doctrineAnnotationTokens[$nextTokenIndex];
        // next token must be nested annotation, we don't care otherwise
        return $nextToken->isType(\ECSPrefix20210710\Doctrine\Common\Annotations\DocLexer::T_AT);
    }
}
