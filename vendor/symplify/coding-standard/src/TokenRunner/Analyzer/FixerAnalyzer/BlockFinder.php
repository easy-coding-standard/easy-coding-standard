<?php

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Exception\MissingImplementationException;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Throwable;

final class BlockFinder
{
    /**
     * @var array<string, int>
     */
    const CONTENT_TO_BLOCK_TYPE = [
        '(' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
        ')' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
        '[' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
        ']' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
        '{' => Tokens::BLOCK_TYPE_CURLY_BRACE,
        '}' => Tokens::BLOCK_TYPE_CURLY_BRACE,
    ];

    /**
     * @var string[]
     */
    const START_EDGES = ['(', '[', '{'];

    /**
     * Accepts position to both start and end token, e.g. (, ), [, ], {, } also to: "array"(, "function" ...(, "use"(,
     * "new" ...(
     *
     * @param Tokens<Token> $tokens
     * @return \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo|null
     * @param int $position
     */
    public function findInTokensByEdge(Tokens $tokens, $position)
    {
        $position = (int) $position;
        $token = $tokens[$position];
        if (! $token instanceof Token) {
            return null;
        }

        // shift "array" to "(", event its position
        if ($token->isGivenKind(T_ARRAY)) {
            $position = $tokens->getNextMeaningfulToken($position);
            /** @var Token $token */
            $token = $tokens[$position];
        }

        if ($token->isGivenKind([T_FUNCTION, CT::T_USE_LAMBDA, T_NEW])) {
            $position = $tokens->getNextTokenOfKind($position, ['(', ';']);
            /** @var Token $token */
            $token = $tokens[$position];

            // end of line was sooner => has no block
            if ($token->equals(';')) {
                return null;
            }
        }

        // some invalid code
        if ($position === null) {
            return null;
        }

        $blockType = $this->getBlockTypeByToken($token);

        return $this->createBlockInfo($token, $position, $tokens, $blockType);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo|null
     * @param int $position
     * @param string $content
     */
    public function findInTokensByPositionAndContent(Tokens $tokens, $position, $content)
    {
        $position = (int) $position;
        $content = (string) $content;
        $blockStart = $tokens->getNextTokenOfKind($position, [$content]);
        if ($blockStart === null) {
            return null;
        }

        $blockType = $this->getBlockTypeByContent($content);

        return new BlockInfo($blockStart, $tokens->findBlockEnd($blockType, $blockStart));
    }

    /**
     * @param string $content
     * @return int
     */
    public function getBlockTypeByContent($content)
    {
        $content = (string) $content;
        if (isset(self::CONTENT_TO_BLOCK_TYPE[$content])) {
            return self::CONTENT_TO_BLOCK_TYPE[$content];
        }

        throw new MissingImplementationException(sprintf(
            'Implementation is missing for "%s" in "%s". Just add it to "%s" property with proper block type',
            $content,
            __METHOD__,
            '$contentToBlockType'
        ));
    }

    /**
     * @return int
     */
    private function getBlockTypeByToken(Token $token)
    {
        if ($token->isArray()) {
            if (in_array($token->getContent(), ['[', ']'], true)) {
                return Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE;
            }
            return Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE;
        }

        return $this->getBlockTypeByContent($token->getContent());
    }

    /**
     * @param Tokens<Token> $tokens
     * @return \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo|null
     * @param int $position
     * @param int $blockType
     */
    private function createBlockInfo(Token $token, $position, Tokens $tokens, $blockType)
    {
        $position = (int) $position;
        $blockType = (int) $blockType;
        try {
            if (in_array($token->getContent(), self::START_EDGES, true)) {
                $blockStart = $position;
                $blockEnd = $tokens->findBlockEnd($blockType, $blockStart);
            } else {
                $blockEnd = $position;
                $blockStart = $tokens->findBlockStart($blockType, $blockEnd);
            }
        } catch (Throwable $throwable) {
            // intentionally, no edge found
            return null;
        }

        return new BlockInfo($blockStart, $blockEnd);
    }
}
