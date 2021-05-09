<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenAnalyzer\DocblockRelatedParamNamesResolver;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;

final class MissingParamNameMalformWorker implements MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/QtWnWv/1
     */
    const PARAM_WITHOUT_NAME_REGEX = '#@param ([^$]*?)( ([^$]*?))?\n#';

    /**
     * @var string
     * @see https://regex101.com/r/58YJNy/1
     */
    const PARAM_ANNOTATOIN_START_REGEX = '@param ';

    /**
     * @var string
     * @see https://regex101.com/r/JhugsI/1
     */
    const PARAM_WITH_NAME_REGEX = '#@param(.*?)\$[\w]+(.*?)\n#';

    /**
     * @var DocblockRelatedParamNamesResolver
     */
    private $docblockRelatedParamNamesResolver;

    public function __construct(DocblockRelatedParamNamesResolver $docblockRelatedParamNamesResolver)
    {
        $this->docblockRelatedParamNamesResolver = $docblockRelatedParamNamesResolver;
    }

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
     * @return mixed[]
     * @param string $docContent
     */
    private function filterOutExistingParamNames($docContent, array $functionArgumentNames)
    {
        $docContent = (string) $docContent;
        foreach ($functionArgumentNames as $key => $functionArgumentName) {
            $pattern = '# ' . preg_quote($functionArgumentName, '#') . '\b#';
            if (Strings::match($docContent, $pattern)) {
                unset($functionArgumentNames[$key]);
            }
        }

        return array_values($functionArgumentNames);
    }

    /**
     * @param string[] $missingArgumentNames
     * @param string[] $argumentNames
     * @return void
     */
    private function completeMissingArgumentNames(
        array $missingArgumentNames,
        array $argumentNames,
        DocBlock $docBlock
    ) {
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
        $missingArgumentName = (string) $missingArgumentName;
        $key = (int) $key;
        if (array_search($missingArgumentName, $argumentNames, true)) {
            return $missingArgumentName;
        }

        return $argumentNames[$key];
    }

    /**
     * @return bool
     */
    private function shouldSkipLine(Line $line)
    {
        if (! Strings::contains($line->getContent(), self::PARAM_ANNOTATOIN_START_REGEX)) {
            return true;
        }

        // already has a param name
        if (Strings::match($line->getContent(), self::PARAM_WITH_NAME_REGEX)) {
            return true;
        }

        $match = Strings::match($line->getContent(), self::PARAM_WITHOUT_NAME_REGEX);
        return $match === null;
    }

    /**
     * @param string $newArgumentName
     * @return string
     */
    private function createNewLineContent($newArgumentName, Line $line)
    {
        $newArgumentName = (string) $newArgumentName;
        // @see https://regex101.com/r/4FL49H/1
        $missingDollarSignPattern = '#(@param\s+([\w\|\[\]\\\\]+\s)?)(' . ltrim($newArgumentName, '$') . ')#';

        // missing \$ case - possibly own worker
        if (Strings::match($line->getContent(), $missingDollarSignPattern)) {
            return Strings::replace($line->getContent(), $missingDollarSignPattern, '$1$$3');
        }

        $replacement = '@param $1 ' . $newArgumentName . '$2' . StaticEolConfiguration::getEolChar();

        return Strings::replace($line->getContent(), self::PARAM_WITHOUT_NAME_REGEX, $replacement);
    }
}
