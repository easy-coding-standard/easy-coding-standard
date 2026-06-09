<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use Symplify\CodingStandard\Utils\Regex;
/**
 * Removes dead param annotation lines that only contain a variable name and no type,
 * e.g. "param $value" - such line carries no information and can be removed.
 */
final class DeadParamMalformWorker implements MalformWorkerInterface
{
    /**
     * @see https://regex101.com/r/Hk4lFc/1
     * @var string
     */
    private const PARAM_WITHOUT_TYPE_REGEX = '#@(?:psalm-|phpstan-)?param\s+\$\w+\s*$#';
    /**
     * @param Tokens<Token> $tokens
     */
    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        $docBlock = new DocBlock($docContent);
        foreach ($docBlock->getLines() as $line) {
            if (!Regex::match($line->getContent(), self::PARAM_WITHOUT_TYPE_REGEX)) {
                continue;
            }
            $line->remove();
        }
        return $docBlock->getContent();
    }
}
