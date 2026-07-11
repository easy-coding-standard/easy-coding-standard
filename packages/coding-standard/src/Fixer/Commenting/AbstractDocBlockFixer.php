<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use Override;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Traverser\TokenReverser;
use Symplify\CodingStandard\Utils\Regex;
/**
 * Base for single-task @param/@return/@var doc block fixers. Handles the shared token traversal,
 * leaving the actual doc block transformation to the child via processDocContent().
 */
abstract class AbstractDocBlockFixer extends AbstractSymplifyFixer
{
    /**
     * @readonly
     * @var \Symplify\CodingStandard\TokenRunner\Traverser\TokenReverser
     */
    protected $tokenReverser;
    /**
     * @see https://regex101.com/r/Nlxkd9/1
     * @var string
     */
    private const TYPE_ANNOTATION_REGEX = '#@(psalm-|phpstan-)?(param|return|var)#';
    public function __construct(TokenReverser $tokenReverser)
    {
        $this->tokenReverser = $tokenReverser;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        if (!$tokens->isAnyTokenKindsFound([\T_DOC_COMMENT, \T_COMMENT])) {
            return \false;
        }
        $reversedTokens = $this->tokenReverser->reverse($tokens);
        foreach ($reversedTokens as $index => $token) {
            if (!$token->isGivenKind([\T_CALLABLE])) {
                continue;
            }
            if (!isset($tokens[$index + 3])) {
                continue;
            }
            if ($tokens[$index + 3]->getContent() !== ')') {
                continue;
            }
            return \false;
        }
        return $tokens->isAnyTokenKindsFound([\T_FUNCTION, \T_VARIABLE]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens): void
    {
        $reversedTokens = $this->tokenReverser->reverse($tokens);
        foreach ($reversedTokens as $index => $token) {
            if (!$token->isGivenKind([\T_DOC_COMMENT, \T_COMMENT])) {
                continue;
            }
            $docContent = $token->getContent();
            if (!Regex::match($docContent, self::TYPE_ANNOTATION_REGEX)) {
                continue;
            }
            $newDocContent = $this->processDocContent($docContent, $tokens, $index);
            if ($newDocContent === $docContent) {
                continue;
            }
            // doc block became empty after removing dead lines → remove it completely,
            // including the whitespace that followed it, to avoid leaving a blank line
            if ($this->isEmptyDocBlock($newDocContent)) {
                $tokens->clearAt($index);
                if (isset($tokens[$index + 1]) && $tokens[$index + 1]->isWhitespace()) {
                    $tokens->clearAt($index + 1);
                }
                continue;
            }
            $tokens[$index] = new Token([\T_DOC_COMMENT, $newDocContent]);
        }
    }
    /**
     * Must run before
     *
     * @see \PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer::getPriority()
     */
    #[Override]
    public function getPriority(): int
    {
        return -37;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    abstract protected function processDocContent(string $docContent, Tokens $tokens, int $position): string;
    private function isEmptyDocBlock(string $docContent): bool
    {
        return Regex::replace($docContent, '#/\*\*|\*/|\*|\s#', '') === '';
    }
}
