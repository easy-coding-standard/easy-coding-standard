<?php

declare (strict_types=1);
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
        $this->symfonyClosureContainerConfiguratorFunctionTokens = [new Token('('), new Token([\T_STRING, 'ContainerConfigurator']), new Token([\T_VARIABLE, '$containerConfigurator']), new Token(')')];
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isContainerConfiguratorClosure(Tokens $tokens) : bool
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
