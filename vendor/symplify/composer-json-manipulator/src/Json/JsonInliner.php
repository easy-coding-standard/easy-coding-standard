<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ComposerJsonManipulator\Json;

use ConfigTransformer20210601\Nette\Utils\Strings;
use ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\Option;
use ConfigTransformer20210601\Symplify\PackageBuilder\Parameter\ParameterProvider;
final class JsonInliner
{
    /**
     * @var string
     * @see https://regex101.com/r/jhWo9g/1
     */
    const SPACE_REGEX = '#\\s+#';
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\ConfigTransformer20210601\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    public function inlineSections(string $jsonContent) : string
    {
        if (!$this->parameterProvider->hasParameter(\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\Option::INLINE_SECTIONS)) {
            return $jsonContent;
        }
        $inlineSections = $this->parameterProvider->provideArrayParameter(\ConfigTransformer20210601\Symplify\ComposerJsonManipulator\ValueObject\Option::INLINE_SECTIONS);
        foreach ($inlineSections as $inlineSection) {
            $pattern = '#("' . \preg_quote($inlineSection, '#') . '": )\\[(.*?)\\](,)#ms';
            $jsonContent = \ConfigTransformer20210601\Nette\Utils\Strings::replace($jsonContent, $pattern, function (array $match) : string {
                $inlined = \ConfigTransformer20210601\Nette\Utils\Strings::replace($match[2], self::SPACE_REGEX, ' ');
                $inlined = \trim($inlined);
                $inlined = '[' . $inlined . ']';
                return $match[1] . $inlined . $match[3];
            });
        }
        return $jsonContent;
    }
}
