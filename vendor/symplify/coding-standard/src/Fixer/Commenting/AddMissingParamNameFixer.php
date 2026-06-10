<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver;
use Symplify\CodingStandard\TokenRunner\Traverser\TokenReverser;
use Symplify\CodingStandard\Utils\Regex;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\AddMissingParamNameFixer\AddMissingParamNameFixerTest
 */
final class AddMissingParamNameFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @readonly
     * @var \Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver
     */
    private $docblockRelatedParamNamesResolver;
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Add a missing variable name to a @param annotation';
    /**
     * @see https://regex101.com/r/QtWnWv/6
     * @var string
     */
    private const PARAM_WITHOUT_NAME_REGEX = '#@param ([^${<]*?)( ([^$]*?))?\n#';
    /**
     * @see https://regex101.com/r/58YJNy/1
     * @var string
     */
    private const PARAM_ANNOTATOIN_START_REGEX = '@param ';
    /**
     * @see https://regex101.com/r/JhugsI/1
     * @var string
     */
    private const PARAM_WITH_NAME_REGEX = '#@param(.*?)\$[\w]+(.*?)\n#';
    public function __construct(TokenReverser $tokenReverser, DocblockRelatedParamNamesResolver $docblockRelatedParamNamesResolver)
    {
        $this->docblockRelatedParamNamesResolver = $docblockRelatedParamNamesResolver;
        parent::__construct($tokenReverser);
    }
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        $argumentNames = $this->docblockRelatedParamNamesResolver->resolve($tokens, $position);
        if ($argumentNames === []) {
            return $docContent;
        }
        $missingArgumentNames = $this->filterOutExistingParamNames($docContent, $argumentNames);
        if ($missingArgumentNames === []) {
            return $docContent;
        }
        $docBlock = new DocBlock($docContent);
        $this->completeMissingArgumentNames($missingArgumentNames, $argumentNames, $docBlock);
        return $docBlock->getContent();
    }
    /**
     * @param string[] $functionArgumentNames
     * @return string[]
     */
    private function filterOutExistingParamNames(string $docContent, array $functionArgumentNames): array
    {
        foreach ($functionArgumentNames as $key => $functionArgumentName) {
            $pattern = '# ' . preg_quote($functionArgumentName, '#') . '\b#';
            if (Regex::match($docContent, $pattern)) {
                unset($functionArgumentNames[$key]);
            }
        }
        return array_values($functionArgumentNames);
    }
    /**
     * @param string[] $missingArgumentNames
     * @param string[] $argumentNames
     */
    private function completeMissingArgumentNames(array $missingArgumentNames, array $argumentNames, DocBlock $docBlock): void
    {
        foreach ($missingArgumentNames as $key => $missingArgumentName) {
            $newArgumentName = $this->resolveNewArgumentName($argumentNames, $missingArgumentName, $key);
            $lines = $docBlock->getLines();
            foreach ($lines as $line) {
                if ($this->shouldSkipLine($line)) {
                    continue;
                }
                $newLineContent = $this->createNewLineContent($newArgumentName, $line);
                $line->setContent($newLineContent);
                continue 2;
            }
        }
    }
    /**
     * @param string[] $argumentNames
     */
    private function resolveNewArgumentName(array $argumentNames, string $missingArgumentName, int $key): string
    {
        if (array_search($missingArgumentName, $argumentNames, \true)) {
            return $missingArgumentName;
        }
        return $argumentNames[$key];
    }
    private function shouldSkipLine(Line $line): bool
    {
        if (strpos($line->getContent(), self::PARAM_ANNOTATOIN_START_REGEX) === \false) {
            return \true;
        }
        // already has a param name
        if (Regex::match($line->getContent(), self::PARAM_WITH_NAME_REGEX)) {
            return \true;
        }
        $match = Regex::match($line->getContent(), self::PARAM_WITHOUT_NAME_REGEX);
        return $match === null;
    }
    private function createNewLineContent(string $newArgumentName, Line $line): string
    {
        // @see https://regex101.com/r/4FL49H/1
        $missingDollarSignPattern = '#(@param\s+([\w\|\[\]\\\\]+\s)?)(' . ltrim($newArgumentName, '$') . ')#';
        // missing \$ case - possibly own worker
        if (Regex::match($line->getContent(), $missingDollarSignPattern)) {
            return Regex::replace($line->getContent(), $missingDollarSignPattern, '$1$$3');
        }
        $replacement = '@param $1 ' . $newArgumentName . '$2' . "\n";
        return Regex::replace($line->getContent(), self::PARAM_WITHOUT_NAME_REGEX, $replacement);
    }
}
