<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;

final class SwitchedTypeAndNameMalformWorker implements MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/4us32A/1
     */
    const NAME_THEN_TYPE_REGEX = '#@(param|var)(\s+)(?<name>\$\w+)(\s+)(?<type>[|\\\\\w\[\]]+)#';

    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     * @param int $position
     * @return string
     */
    public function work($docContent, Tokens $tokens, $position)
    {
        $docContent = (string) $docContent;
        $position = (int) $position;
        $docBlock = new DocBlock($docContent);

        $lines = $docBlock->getLines();
        foreach ($lines as $line) {
            // $value is first, instead of type is first
            $match = Strings::match($line->getContent(), self::NAME_THEN_TYPE_REGEX);
            if ($match === null) {
                continue;
            }

            if ($match['name'] === '') {
                continue;
            }

            if ($match['type'] === '') {
                continue;
            }

            // skip random words that look like type without autolaoding
            if (in_array($match['type'], ['The', 'Set'], true)) {
                continue;
            }

            $newLine = Strings::replace($line->getContent(), self::NAME_THEN_TYPE_REGEX, '@$1$2$5$4$3');
            $line->setContent($newLine);
        }

        return $docBlock->getContent();
    }
}
