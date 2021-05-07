<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20210507\Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
final class MissingParamNameMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/QtWnWv/1
     */
    const PARAM_WITHOUT_NAME_REGEX = '#@param ([^$]*?)( ([^$]*?))?\\n#';
    /**
     * @var string
     * @see https://regex101.com/r/58YJNy/1
     */
    const PARAM_ANNOTATOIN_START_REGEX = '@param ';
    /**
     * @var string
     * @see https://regex101.com/r/JhugsI/1
     */
    const PARAM_WITH_NAME_REGEX = '#@param(.*?)\\$[\\w]+(.*?)\\n#';
    /**
     * @var DocblockRelatedParamNamesResolver
     */
    private $docblockRelatedParamNamesResolver;
    /**
     * @param \Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver $docblockRelatedParamNamesResolver
     */
    public function __construct($docblockRelatedParamNamesResolver)
    {
        $this->docblockRelatedParamNamesResolver = $docblockRelatedParamNamesResolver;
    }
    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     * @param int $position
     * @return string
     */
    public function work($docContent, $tokens, $position)
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
     * @return mixed[]
     * @param string $docContent
     */
    private function filterOutExistingParamNames($docContent, array $functionArgumentNames)
    {
        foreach ($functionArgumentNames as $key => $functionArgumentName) {
            $pattern = '# ' . \preg_quote($functionArgumentName, '#') . '\\b#';
            if (\ECSPrefix20210507\Nette\Utils\Strings::match($docContent, $pattern)) {
                unset($functionArgumentNames[$key]);
            }
        }
        return \array_values($functionArgumentNames);
    }
    /**
     * @param string[] $missingArgumentNames
     * @param string[] $argumentNames
     * @return void
     * @param \PhpCsFixer\DocBlock\DocBlock $docBlock
     */
    private function completeMissingArgumentNames(array $missingArgumentNames, array $argumentNames, $docBlock)
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
     * @param string $missingArgumentName
     * @param int $key
     * @return string
     */
    private function resolveNewArgumentName(array $argumentNames, $missingArgumentName, $key)
    {
        if (\array_search($missingArgumentName, $argumentNames, \true)) {
            return $missingArgumentName;
        }
        return $argumentNames[$key];
    }
    /**
     * @param \PhpCsFixer\DocBlock\Line $line
     * @return bool
     */
    private function shouldSkipLine($line)
    {
        if (!\ECSPrefix20210507\Nette\Utils\Strings::contains($line->getContent(), self::PARAM_ANNOTATOIN_START_REGEX)) {
            return \true;
        }
        // already has a param name
        if (\ECSPrefix20210507\Nette\Utils\Strings::match($line->getContent(), self::PARAM_WITH_NAME_REGEX)) {
            return \true;
        }
        $match = \ECSPrefix20210507\Nette\Utils\Strings::match($line->getContent(), self::PARAM_WITHOUT_NAME_REGEX);
        return $match === null;
    }
    /**
     * @param string $newArgumentName
     * @param \PhpCsFixer\DocBlock\Line $line
     * @return string
     */
    private function createNewLineContent($newArgumentName, $line)
    {
        // @see https://regex101.com/r/4FL49H/1
        $missingDollarSignPattern = '#(@param\\s+([\\w\\|\\[\\]\\\\]+\\s)?)(' . \ltrim($newArgumentName, '$') . ')#';
        // missing \$ case - possibly own worker
        if (\ECSPrefix20210507\Nette\Utils\Strings::match($line->getContent(), $missingDollarSignPattern)) {
            return \ECSPrefix20210507\Nette\Utils\Strings::replace($line->getContent(), $missingDollarSignPattern, '$1$$3');
        }
        $replacement = '@param $1 ' . $newArgumentName . '$2' . \Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar();
        return \ECSPrefix20210507\Nette\Utils\Strings::replace($line->getContent(), self::PARAM_WITHOUT_NAME_REGEX, $replacement);
    }
}
