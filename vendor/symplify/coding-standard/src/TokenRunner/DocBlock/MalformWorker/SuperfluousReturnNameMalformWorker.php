<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20210515\Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
final class SuperfluousReturnNameMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/26Wy7Y/1
     */
    const RETURN_VARIABLE_NAME_REGEX = '#(?<tag>@return)(?<type>\\s+[|\\\\\\w]+)?(\\s+)(?<' . self::VARIABLE_NAME_PART . '>\\$[\\w]+)#';
    /**
     * @var string[]
     */
    const ALLOWED_VARIABLE_NAMES = ['$this'];
    /**
     * @var string
     * @see https://regex101.com/r/IE9fA6/1
     */
    const VARIABLE_NAME_REGEX = '#\\$\\w+#';
    /**
     * @var string
     */
    const VARIABLE_NAME_PART = 'variableName';
    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     * @param int $position
     * @return string
     */
    public function work($docContent, \PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $docContent = (string) $docContent;
        $position = (int) $position;
        $docBlock = new \PhpCsFixer\DocBlock\DocBlock($docContent);
        $lines = $docBlock->getLines();
        foreach ($lines as $line) {
            $match = \ECSPrefix20210515\Nette\Utils\Strings::match($line->getContent(), self::RETURN_VARIABLE_NAME_REGEX);
            if ($match === null) {
                continue;
            }
            if ($this->shouldSkip($match, $line->getContent())) {
                continue;
            }
            $newLineContent = \ECSPrefix20210515\Nette\Utils\Strings::replace($line->getContent(), self::RETURN_VARIABLE_NAME_REGEX, function (array $match) {
                $replacement = $match['tag'];
                if ($match['type'] !== []) {
                    $replacement .= $match['type'];
                }
                return $replacement;
            });
            $line->setContent($newLineContent);
        }
        return $docBlock->getContent();
    }
    /**
     * @param array<string, string> $match
     * @param string $content
     * @return bool
     */
    private function shouldSkip(array $match, $content)
    {
        $content = (string) $content;
        if (\in_array($match[self::VARIABLE_NAME_PART], self::ALLOWED_VARIABLE_NAMES, \true)) {
            return \true;
        }
        // has multiple return values? "@return array $one, $two"
        return \count(\ECSPrefix20210515\Nette\Utils\Strings::matchAll($content, self::VARIABLE_NAME_REGEX)) >= 2;
    }
}
