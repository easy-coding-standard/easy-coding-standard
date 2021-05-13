<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20210513\Nette\Utils\Strings;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
final class ParamNameTypoMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/5szHlw/1
     */
    const PARAM_NAME_REGEX = '#@param(.*?)(?<paramName>\\$\\w+)#';
    /**
     * @var DocblockRelatedParamNamesResolver
     */
    private $docblockRelatedParamNamesResolver;
    public function __construct(\Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver $docblockRelatedParamNamesResolver)
    {
        $this->docblockRelatedParamNamesResolver = $docblockRelatedParamNamesResolver;
    }
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
        $argumentNames = $this->docblockRelatedParamNamesResolver->resolve($tokens, $position);
        if ($argumentNames === []) {
            return $docContent;
        }
        $paramNames = $this->getParamNames($docContent);
        // remove correct params
        foreach ($argumentNames as $key => $argumentName) {
            if (\in_array($argumentName, $paramNames, \true)) {
                $paramPosition = \array_search($argumentName, $paramNames, \true);
                unset($paramNames[$paramPosition]);
                unset($argumentNames[$key]);
            }
        }
        // nothing to edit, all arguments are correct or there are no more @param annotations
        if ($argumentNames === []) {
            return $docContent;
        }
        if ($paramNames === []) {
            return $docContent;
        }
        return $this->fixTypos($argumentNames, $paramNames, $docContent);
    }
    /**
     * @return mixed[]
     * @param string $docContent
     */
    private function getParamNames($docContent)
    {
        $docContent = (string) $docContent;
        $paramAnnotations = $this->getAnnotationsOfType($docContent, 'param');
        $paramNames = [];
        foreach ($paramAnnotations as $paramAnnotation) {
            $match = \ECSPrefix20210513\Nette\Utils\Strings::match($paramAnnotation->getContent(), self::PARAM_NAME_REGEX);
            if (isset($match['paramName'])) {
                $paramNames[] = $match['paramName'];
            }
        }
        return $paramNames;
    }
    /**
     * @return mixed[]
     * @param string $docContent
     * @param string $type
     */
    private function getAnnotationsOfType($docContent, $type)
    {
        $docContent = (string) $docContent;
        $type = (string) $type;
        $docBlock = new \PhpCsFixer\DocBlock\DocBlock($docContent);
        return $docBlock->getAnnotationsOfType($type);
    }
    /**
     * @param string[] $argumentNames
     * @param string[] $paramNames
     * @param string $docContent
     * @return string
     */
    private function fixTypos(array $argumentNames, array $paramNames, $docContent)
    {
        $docContent = (string) $docContent;
        foreach ($argumentNames as $key => $argumentName) {
            // 1. the same position
            if (!isset($paramNames[$key])) {
                continue;
            }
            $typoName = $paramNames[$key];
            $replacePattern = '#@param(.*?)' . \preg_quote($typoName, '#') . '#';
            $docContent = \ECSPrefix20210513\Nette\Utils\Strings::replace($docContent, $replacePattern, '@param$1' . $argumentName);
        }
        return $docContent;
    }
}
