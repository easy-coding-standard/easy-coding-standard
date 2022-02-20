<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\ComposerJsonManipulator\Json;

use ECSPrefix20220220\Nette\Utils\Strings;
use ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\Option;
use ECSPrefix20220220\Symplify\PackageBuilder\Parameter\ParameterProvider;
final class JsonInliner
{
    /**
     * @var string
     * @see https://regex101.com/r/jhWo9g/1
     */
    private const SPACE_REGEX = '#\\s+#';
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\ECSPrefix20220220\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    public function inlineSections(string $jsonContent) : string
    {
        if (!$this->parameterProvider->hasParameter(\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\Option::INLINE_SECTIONS)) {
            return $jsonContent;
        }
        $inlineSections = $this->parameterProvider->provideArrayParameter(\ECSPrefix20220220\Symplify\ComposerJsonManipulator\ValueObject\Option::INLINE_SECTIONS);
        foreach ($inlineSections as $inlineSection) {
            $pattern = '#("' . \preg_quote($inlineSection, '#') . '": )\\[(.*?)\\](,)#ms';
            $jsonContent = \ECSPrefix20220220\Nette\Utils\Strings::replace($jsonContent, $pattern, function (array $match) : string {
                $inlined = \ECSPrefix20220220\Nette\Utils\Strings::replace($match[2], self::SPACE_REGEX, ' ');
                $inlined = \trim($inlined);
                $inlined = '[' . $inlined . ']';
                return $match[1] . $inlined . $match[3];
            });
        }
        return $jsonContent;
    }
}
