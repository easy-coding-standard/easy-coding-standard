<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20220220\Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use ECSPrefix20220220\Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
final class MissingParamNameMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/QtWnWv/5
     */
    private const PARAM_WITHOUT_NAME_REGEX = '#@param ([^${]*?)( ([^$]*?))?\\n#';
    /**
     * @var string
     * @see https://regex101.com/r/58YJNy/1
     */
    private const PARAM_ANNOTATOIN_START_REGEX = '@param ';
    /**
     * @var string
     * @see https://regex101.com/r/JhugsI/1
     */
    private const PARAM_WITH_NAME_REGEX = '#@param(.*?)\\$[\\w]+(.*?)\\n#';
    /**
     * @var \Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver
     */
    private $docblockRelatedParamNamesResolver;
    public function __construct(\Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver $docblockRelatedParamNamesResolver)
    {
        $this->docblockRelatedParamNamesResolver = $docblockRelatedParamNamesResolver;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function work(string $docContent, \PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : string
    {
        $argumentNames = $this->docblockRelatedParamNamesResolver->resolve($tokens, $position);
        if ($argumentNames === []) {
            return $docContent;
        }
        $missingArgumentNames = $this->filterOutExistingParamNames($docContent, $argumentNames);
        if ($missingArgumentNames === []) {
            return $docContent;
        }
        $docBlock = new \PhpCsFixer\DocBlock\DocBlock($docContent);
        $this->completeMissingArgumentNames($missingArgumentNames, $argumentNames, $docBlock);
        return $docBlock->getContent();
    }
    /**
     * @param string[] $functionArgumentNames
     * @return string[]
     */
    private function filterOutExistingParamNames(string $docContent, array $functionArgumentNames) : array
    {
        foreach ($functionArgumentNames as $key => $functionArgumentName) {
            $pattern = '# ' . \preg_quote($functionArgumentName, '#') . '\\b#';
            if (\ECSPrefix20220220\Nette\Utils\Strings::match($docContent, $pattern)) {
                unset($functionArgumentNames[$key]);
            }
        }
        return \array_values($functionArgumentNames);
    }
    /**
     * @param string[] $missingArgumentNames
     * @param string[] $argumentNames
     */
    private function completeMissingArgumentNames(array $missingArgumentNames, array $argumentNames, \PhpCsFixer\DocBlock\DocBlock $docBlock) : void
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
    private function resolveNewArgumentName(array $argumentNames, string $missingArgumentName, int $key) : string
    {
        if (\array_search($missingArgumentName, $argumentNames, \true)) {
            return $missingArgumentName;
        }
        return $argumentNames[$key];
    }
    private function shouldSkipLine(\PhpCsFixer\DocBlock\Line $line) : bool
    {
        if (\strpos($line->getContent(), self::PARAM_ANNOTATOIN_START_REGEX) === \false) {
            return \true;
        }
        // already has a param name
        if (\ECSPrefix20220220\Nette\Utils\Strings::match($line->getContent(), self::PARAM_WITH_NAME_REGEX)) {
            return \true;
        }
        $match = \ECSPrefix20220220\Nette\Utils\Strings::match($line->getContent(), self::PARAM_WITHOUT_NAME_REGEX);
        return $match === null;
    }
    private function createNewLineContent(string $newArgumentName, \PhpCsFixer\DocBlock\Line $line) : string
    {
        // @see https://regex101.com/r/4FL49H/1
        $missingDollarSignPattern = '#(@param\\s+([\\w\\|\\[\\]\\\\]+\\s)?)(' . \ltrim($newArgumentName, '$') . ')#';
        // missing \$ case - possibly own worker
        if (\ECSPrefix20220220\Nette\Utils\Strings::match($line->getContent(), $missingDollarSignPattern)) {
            return \ECSPrefix20220220\Nette\Utils\Strings::replace($line->getContent(), $missingDollarSignPattern, '$1$$3');
        }
        $replacement = '@param $1 ' . $newArgumentName . '$2' . \ECSPrefix20220220\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar();
        return \ECSPrefix20220220\Nette\Utils\Strings::replace($line->getContent(), self::PARAM_WITHOUT_NAME_REGEX, $replacement);
    }
}
