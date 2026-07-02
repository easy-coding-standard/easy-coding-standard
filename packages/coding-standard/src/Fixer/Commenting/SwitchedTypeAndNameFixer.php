<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Utils\Regex;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\SwitchedTypeAndNameFixer\SwitchedTypeAndNameFixerTest
 */
final class SwitchedTypeAndNameFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Reorder switched type and variable name in @param/@var annotation';
    /**
     * @see https://regex101.com/r/4us32A/1
     * @var string
     */
    private const NAME_THEN_TYPE_REGEX = '#@((?:psalm-|phpstan-)?(?:param|var))(\s+)(?<name>\$\w+)(\s+)(?<type>(?:[|\\\\\\w\[\]]|<[^<>]*>)+)(?<rest>\s+\S.*)?$#';
    /**
     * @var string[]
     */
    private const KNOWN_PRIMITIVE_TYPES = ['string', 'int', 'integer', 'float', 'bool', 'boolean', 'array', 'object', 'callable', 'iterable', 'mixed', 'void', 'null', 'false', 'true', 'self', 'static', 'parent', 'resource', 'scalar', 'never', 'number', 'double'];
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        $docBlock = new DocBlock($docContent);
        $lines = $docBlock->getLines();
        foreach ($lines as $line) {
            // $value is first, instead of type is first
            $match = Regex::match($line->getContent(), self::NAME_THEN_TYPE_REGEX);
            if ($match === null) {
                continue;
            }
            if ($match['name'] === '') {
                continue;
            }
            if ($match['type'] === '') {
                continue;
            }
            // skip plain comment words mistaken for a type, e.g. "@param $a Can be used to..."
            if (!$this->isKnownType($match['type'])) {
                continue;
            }
            $newLine = Regex::replace($line->getContent(), self::NAME_THEN_TYPE_REGEX, '@$1$2$5$4$3$6');
            $line->setContent($newLine);
        }
        return $docBlock->getContent();
    }
    private function isKnownType(string $type): bool
    {
        // has type syntax: namespace, generics, array brackets or union
        if (Regex::match($type, '#[\\\\\\[\]<>|]#') !== null) {
            return \true;
        }
        return in_array(strtolower($type), self::KNOWN_PRIMITIVE_TYPES, \true);
    }
}
