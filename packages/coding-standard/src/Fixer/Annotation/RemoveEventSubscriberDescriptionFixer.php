<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Annotation;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\Fixer\Naming\MethodNameResolver;
use Symplify\CodingStandard\TokenRunner\Traverser\TokenReverser;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Annotation\RemoveEventSubscriberDescriptionFixer\RemoveEventSubscriberDescriptionFixerTest
 */
final class RemoveEventSubscriberDescriptionFixer extends AbstractSymplifyFixer
{
    /**
     * @readonly
     * @var \Symplify\CodingStandard\TokenRunner\Traverser\TokenReverser
     */
    private $tokenReverser;
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove event subscriber docblock description that only repeats the method name words plus "event"';
    /**
     * @readonly
     * @var \Symplify\CodingStandard\Fixer\Naming\MethodNameResolver
     */
    private $methodNameResolver;
    public function __construct(TokenReverser $tokenReverser)
    {
        $this->tokenReverser = $tokenReverser;
        $this->methodNameResolver = new MethodNameResolver();
    }
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        if (!$tokens->isTokenKindFound(\T_FUNCTION)) {
            return \false;
        }
        return $tokens->isAnyTokenKindsFound([\T_DOC_COMMENT, \T_COMMENT]);
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
            $functionIndex = $this->resolveFunctionIndex($tokens, $index);
            if ($functionIndex === null) {
                continue;
            }
            // only handle public methods (event subscriber handlers)
            if (!$this->isPublicMethod($tokens, $index, $functionIndex)) {
                continue;
            }
            $methodName = $this->methodNameResolver->resolve($tokens, $index);
            if ($methodName === null) {
                continue;
            }
            $docblockLines = explode("\n", $token->getContent());
            $hasChanged = \false;
            foreach ($docblockLines as $key => $docblockLine) {
                if (!$this->isEventDescriptionLine($docblockLine, $methodName)) {
                    continue;
                }
                unset($docblockLines[$key]);
                $hasChanged = \true;
            }
            if (!$hasChanged) {
                continue;
            }
            if ($this->isEmptyDocblock($docblockLines)) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            } else {
                $tokens[$index] = new Token([\T_DOC_COMMENT, implode("\n", $docblockLines)]);
            }
        }
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function resolveFunctionIndex(Tokens $tokens, int $commentIndex): ?int
    {
        foreach ($tokens as $position => $token) {
            if ($position <= $commentIndex) {
                continue;
            }
            if ($token->isGivenKind(\T_FUNCTION)) {
                return $position;
            }
        }
        return null;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function isPublicMethod(Tokens $tokens, int $commentIndex, int $functionIndex): bool
    {
        for ($position = $commentIndex + 1; $position < $functionIndex; ++$position) {
            if ($tokens[$position]->isGivenKind([\T_PRIVATE, \T_PROTECTED])) {
                return \false;
            }
        }
        return \true;
    }
    private function isEventDescriptionLine(string $docblockLine, string $methodName): bool
    {
        $descriptionWords = $this->resolveWords($docblockLine);
        // must be an "... event" description, otherwise leave it to duplicate-description fixer
        if (!in_array('event', $descriptionWords, \true)) {
            return \false;
        }
        $descriptionWords = array_filter($descriptionWords, static function (string $word): bool {
            return $word !== 'event';
        });
        if ($descriptionWords === []) {
            return \false;
        }
        $methodWords = array_filter(
            $this->resolveWords($methodName),
            // event subscriber handlers are commonly prefixed with "on"
            static function (string $word): bool {
                return $word !== 'on';
            }
        );
        sort($descriptionWords);
        sort($methodWords);
        return $descriptionWords === $methodWords;
    }
    /**
     * @param string[] $docblockLines
     */
    private function isEmptyDocblock(array $docblockLines): bool
    {
        $bareContent = preg_replace('#[/*\s]+#', '', implode('', $docblockLines));
        return $bareContent === '';
    }
    /**
     * @return string[]
     */
    private function resolveWords(string $value): array
    {
        // split camelCase boundaries, e.g. "onLeadDelete" => "on Lead Delete"
        $spaced = (string) preg_replace('#(?<=[a-z])(?=[A-Z])#', ' ', $value);
        preg_match_all('#[a-zA-Z]+#', strtolower($spaced), $matches);
        return $matches[0];
    }
}
