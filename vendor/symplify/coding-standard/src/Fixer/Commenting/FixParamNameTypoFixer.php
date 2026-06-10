<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver;
use Symplify\CodingStandard\TokenRunner\Traverser\TokenReverser;
use Symplify\CodingStandard\Utils\Regex;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\FixParamNameTypoFixer\FixParamNameTypoFixerTest
 */
final class FixParamNameTypoFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @readonly
     * @var \Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver
     */
    private $docblockRelatedParamNamesResolver;
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Fix a typo in the @param variable name to match the real argument';
    /**
     * @see https://regex101.com/r/5szHlw/1
     * @var string
     */
    private const PARAM_NAME_REGEX = '#@param(\s+)(?<callable>callable)?(.*?)(?<paramName>\$\w+)#';
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
        $paramNames = $this->getParamNames($docContent);
        $missArgumentNames = [];
        // remove correct params
        foreach ($argumentNames as $key => $argumentName) {
            if (in_array($argumentName, $paramNames, \true)) {
                $paramPosition = array_search($argumentName, $paramNames, \true);
                unset($paramNames[$paramPosition]);
            } else {
                $missArgumentNames[$key] = $argumentName;
            }
        }
        // nothing to edit, all arguments are correct or there are no more @param annotations
        if ($missArgumentNames === []) {
            return $docContent;
        }
        if ($paramNames === []) {
            return $docContent;
        }
        return $this->fixTypos($argumentNames, $missArgumentNames, $paramNames, $docContent);
    }
    /**
     * @return string[]
     */
    private function getParamNames(string $docContent): array
    {
        $paramAnnotations = $this->getAnnotationsOfType($docContent, 'param');
        $paramNames = [];
        foreach ($paramAnnotations as $paramAnnotation) {
            $match = Regex::match($paramAnnotation->getContent(), self::PARAM_NAME_REGEX);
            if (isset($match['paramName'])) {
                // skip callables, as they contain nested params
                if (isset($match['callable']) && $match['callable'] === 'callable') {
                    continue;
                }
                $paramNames[] = $match['paramName'];
            }
        }
        return $paramNames;
    }
    /**
     * @return Annotation[]
     */
    private function getAnnotationsOfType(string $docContent, string $type): array
    {
        $docBlock = new DocBlock($docContent);
        return $docBlock->getAnnotationsOfType($type);
    }
    /**
     * @param string[] $argumentNames
     * @param string[] $missArgumentNames
     * @param string[] $paramNames
     */
    private function fixTypos(array $argumentNames, array $missArgumentNames, array $paramNames, string $docContent): string
    {
        // A table of permuted params. initialized by $argumentName instead of $paramNames is correct
        $replacedParams = array_fill_keys($argumentNames, \false);
        foreach ($missArgumentNames as $key => $argumentName) {
            // 1. the same position
            if (!isset($paramNames[$key])) {
                continue;
            }
            $typoName = $paramNames[$key];
            $replacePattern = '#@param(.*?)(' . preg_quote($typoName, '#') . '\b)#';
            $docContent = Regex::replace($docContent, $replacePattern, static function (array $matched) use ($argumentName, &$replacedParams) {
                $paramName = $matched[2];
                // 2. If the PHPDoc $paramName is one of the existing $argumentNames and has not already been replaced, it will be deferred
                if (isset($replacedParams[$paramName]) && !$replacedParams[$paramName]) {
                    $replacedParams[$paramName] = \true;
                    return $matched[0];
                }
                // 3. Otherwise, replace $paramName with $argumentName in the @param line
                return sprintf('@param%s%s', $matched[1], $argumentName);
            });
        }
        return $docContent;
    }
}
