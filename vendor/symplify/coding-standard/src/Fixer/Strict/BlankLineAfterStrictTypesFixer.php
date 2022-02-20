<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Strict;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * Inspired at https://github.com/aidantwoods/PHP-CS-Fixer/tree/feature/DeclareStrictTypesFixer-split
 *
 * @thanks Aidan Woods
 * @see \Symplify\CodingStandard\Tests\Fixer\Strict\BlankLineAfterStrictTypesFixer\BlankLineAfterStrictTypesFixerTest
 */
final class BlankLineAfterStrictTypesFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Strict type declaration has to be followed by empty line';
    /**
     * Generates: "declare(strict_types=1);"
     *
     * @var Token[]
     */
    private $declareStrictTypeTokens = [];
    /**
     * @var \PhpCsFixer\WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;
    public function __construct(\PhpCsFixer\WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->declareStrictTypeTokens = [new \PhpCsFixer\Tokenizer\Token([\T_DECLARE, 'declare']), new \PhpCsFixer\Tokenizer\Token('('), new \PhpCsFixer\Tokenizer\Token([\T_STRING, 'strict_types']), new \PhpCsFixer\Tokenizer\Token('='), new \PhpCsFixer\Tokenizer\Token([\T_LNUMBER, '1']), new \PhpCsFixer\Tokenizer\Token(')'), new \PhpCsFixer\Tokenizer\Token(';')];
    }
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAllTokenKindsFound([\T_OPEN_TAG, \T_WHITESPACE, \T_DECLARE, \T_STRING, '=', \T_LNUMBER, ';']);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(\SplFileInfo $fileInfo, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $sequenceLocation = $tokens->findSequence($this->declareStrictTypeTokens, 1, 15);
        if ($sequenceLocation === null) {
            return;
        }
        \end($sequenceLocation);
        $semicolonPosition = (int) \key($sequenceLocation);
        // empty file
        if (!isset($tokens[$semicolonPosition + 2])) {
            return;
        }
        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();
        $tokens->ensureWhitespaceAtIndex($semicolonPosition + 1, 0, $lineEnding . $lineEnding);
    }
    public function getRuleDefinition() : \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
declare(strict_types=1);
namespace App;
CODE_SAMPLE
, <<<'CODE_SAMPLE'
declare(strict_types=1);

namespace App;
CODE_SAMPLE
)]);
    }
}
