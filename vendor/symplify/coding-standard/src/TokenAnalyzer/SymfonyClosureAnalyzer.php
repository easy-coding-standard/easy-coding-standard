<?php

namespace Symplify\CodingStandard\TokenAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class SymfonyClosureAnalyzer
{
    /**
     * @var Token[]
     */
    private $symfonyClosureContainerConfiguratorFunctionTokens = [];
    public function __construct()
    {
        $this->symfonyClosureContainerConfiguratorFunctionTokens = [new \PhpCsFixer\Tokenizer\Token('('), new \PhpCsFixer\Tokenizer\Token([\T_STRING, 'ContainerConfigurator']), new \PhpCsFixer\Tokenizer\Token([\T_VARIABLE, '$containerConfigurator']), new \PhpCsFixer\Tokenizer\Token(')')];
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    public function isContainerConfiguratorClosure(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            $closureSequence = $tokens->findSequence($this->symfonyClosureContainerConfiguratorFunctionTokens);
            if ($closureSequence === null) {
                continue;
            }
            return \true;
        }
        return \false;
    }
}
