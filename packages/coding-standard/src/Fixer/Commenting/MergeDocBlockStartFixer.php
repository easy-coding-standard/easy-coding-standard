<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
/**
 * Turns a malformed single-asterisk "/*" doc block into a proper "/**" one and drops its
 * empty "*"-only lines, e.g. a blank line right after the opener or between description and tags.
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\MergeDocBlockStartFixer\MergeDocBlockStartFixerTest
 */
final class MergeDocBlockStartFixer extends AbstractSymplifyFixer
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use a "/**" doc block opener and drop empty asterisk-only lines';
    /**
     * @see https://regex101.com/r/qL3xkP/1
     * @var string
     */
    private const EMPTY_ASTERISK_LINE_REGEX = '#^\s*\*\s*$#';
    /**
     * @see https://regex101.com/r/8Yb2wv/1
     * @var string
     */
    private const PHPDOC_TAG_LINE_REGEX = '#\n\s*\*\s*@#';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_COMMENT);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_COMMENT)) {
                continue;
            }
            $content = $token->getContent();
            // only block comments that open with a single asterisk "/*"
            if (strncmp($content, '/*', strlen('/*')) !== 0) {
                continue;
            }
            if (strncmp($content, '/**', strlen('/**')) === 0) {
                continue;
            }
            // must be a real doc block, i.e. carry a phpdoc tag ("@param", "@return", ...)
            if (preg_match(self::PHPDOC_TAG_LINE_REGEX, $content) !== 1) {
                continue;
            }
            $lines = explode("\n", $content);
            $emptyLineKeys = $this->resolveEmptyAsteriskLineKeys($lines);
            if ($emptyLineKeys === []) {
                continue;
            }
            foreach ($emptyLineKeys as $emptyLineKey) {
                unset($lines[$emptyLineKey]);
            }
            // "/*" opener → "/**"
            $lines[0] = (string) preg_replace('#^/\*#', '/**', $lines[0]);
            $tokens[$index] = new Token([\T_DOC_COMMENT, implode("\n", $lines)]);
        }
    }
    /**
     * @param string[] $lines
     * @return int[]
     */
    private function resolveEmptyAsteriskLineKeys(array $lines): array
    {
        $emptyLineKeys = [];
        foreach ($lines as $key => $line) {
            // never touch the opener line
            if ($key === 0) {
                continue;
            }
            if (preg_match(self::EMPTY_ASTERISK_LINE_REGEX, $line) === 1) {
                $emptyLineKeys[] = $key;
            }
        }
        return $emptyLineKeys;
    }
}
