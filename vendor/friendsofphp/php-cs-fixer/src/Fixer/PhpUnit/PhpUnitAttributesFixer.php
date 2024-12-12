<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\AttributeNotation\OrderedAttributesFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\AttributeAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Processor\ImportProcessor;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @implements ConfigurableFixerInterface<_AutogeneratedInputConfiguration, _AutogeneratedComputedConfiguration>
 *
 * @phpstan-type _AutogeneratedInputConfiguration array{
 *  keep_annotations?: bool
 * }
 * @phpstan-type _AutogeneratedComputedConfiguration array{
 *  keep_annotations: bool
 * }
 */
final class PhpUnitAttributesFixer extends AbstractPhpUnitFixer implements ConfigurableFixerInterface
{
    /** @use ConfigurableFixerTrait<_AutogeneratedInputConfiguration, _AutogeneratedComputedConfiguration> */
    use ConfigurableFixerTrait;
    /** @var array<string, string> */
    private $fixingMap;
    public function __construct()
    {
        parent::__construct();
        $this->prepareFixingMap();
    }
    public function getDefinition() : FixerDefinitionInterface
    {
        $codeSample = <<<'PHP'
<?php

namespace ECSPrefix202412;

/**
* @covers \VendorName\Foo
* @internal
*/
final class FooTest extends TestCase
{
    /**
    * @param int $expected
    * @param int $actual
    * @dataProvider giveMeSomeData
    * @requires PHP 8.0
    */
    public function testSomething($expected, $actual)
    {
    }
}
/**
* @covers \VendorName\Foo
* @internal
*/
\class_alias('ECSPrefix202412\\FooTest', 'FooTest', \false);

PHP;
        return new FixerDefinition('PHPUnit attributes must be used over their respective PHPDoc-based annotations.', [new VersionSpecificCodeSample($codeSample, new VersionSpecification(80000)), new VersionSpecificCodeSample($codeSample, new VersionSpecification(80000), ['keep_annotations' => \true])]);
    }
    public function isCandidate(Tokens $tokens) : bool
    {
        return \PHP_VERSION_ID >= 80000 && parent::isCandidate($tokens);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before FullyQualifiedStrictTypesFixer, PhpdocSeparationFixer, PhpdocTrimConsecutiveBlankLineSeparationFixer, PhpdocTrimFixer.
     * Must run after PhpUnitDataProviderNameFixer, PhpUnitDataProviderReturnTypeFixer, PhpUnitDataProviderStaticFixer.
     */
    public function getPriority() : int
    {
        return 8;
    }
    protected function createConfigurationDefinition() : FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([(new FixerOptionBuilder('keep_annotations', 'Whether to keep annotations or not. This may be helpful for projects that support PHP before version 8 or PHPUnit before version 10.'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption()]);
    }
    protected function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex) : void
    {
        $classIndex = $tokens->getPrevTokenOfKind($startIndex, [[\T_CLASS]]);
        $docBlockIndex = $this->getDocBlockIndex($tokens, $classIndex);
        if ($tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT)) {
            $startIndex = $docBlockIndex;
        }
        for ($index = $endIndex; $index >= $startIndex; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $targetIndex = $tokens->getTokenNotOfKindSibling($index, 1, [[\T_ABSTRACT], [\T_COMMENT], [\T_FINAL], [\T_FUNCTION], [\T_PRIVATE], [\T_PROTECTED], [\T_PUBLIC], [\T_STATIC], [\T_WHITESPACE]]);
            $annotationScope = $tokens[$targetIndex]->isGivenKind(\T_CLASS) ? 'class' : 'method';
            $docBlock = new DocBlock($tokens[$index]->getContent());
            $presentAttributes = [];
            foreach (\array_reverse($docBlock->getAnnotations()) as $annotation) {
                $annotationName = $annotation->getTag()->getName();
                if (!isset($this->fixingMap[$annotationName])) {
                    continue;
                }
                if (!self::shouldBeFixed($annotationName, $annotationScope)) {
                    continue;
                }
                /** @phpstan-ignore-next-line */
                $tokensToInsert = self::{$this->fixingMap[$annotationName]}($tokens, $index, $annotation);
                $presentAttributes[$annotationName] = $presentAttributes[$annotationName] ?? self::isAttributeAlreadyPresent($tokens, $index, $tokensToInsert);
                if ($presentAttributes[$annotationName]) {
                    continue;
                }
                if ([] === $tokensToInsert) {
                    continue;
                }
                $tokens->insertSlices([$index + 1 => $tokensToInsert]);
                if (!$this->configuration['keep_annotations']) {
                    $annotation->remove();
                }
            }
            if ('' === $docBlock->getContent()) {
                $tokens->clearTokenAndMergeSurroundingWhitespace($index);
            } else {
                $tokens[$index] = new Token([\T_DOC_COMMENT, $docBlock->getContent()]);
            }
        }
    }
    private function prepareFixingMap() : void
    {
        // annotations that map to attribute without parameters
        foreach (['after', 'afterClass', 'before', 'beforeClass', 'coversNothing', 'doesNotPerformAssertions', 'large', 'medium', 'runInSeparateProcess', 'runTestsInSeparateProcesses', 'small', 'test', 'preCondition', 'postCondition'] as $annotation) {
            $this->fixingMap[$annotation] = 'fixWithoutParameters';
        }
        // annotations that map to attribute with single string value
        foreach (['group', 'testDox', 'ticket'] as $annotation) {
            $this->fixingMap[$annotation] = 'fixWithSingleStringValue';
        }
        // annotations that map from 'enabled'/'disabled' value to attribute with boolean value
        foreach (['backupGlobals', 'backupStaticAttributes', 'preserveGlobalState'] as $annotation) {
            $this->fixingMap[$annotation] = 'fixWithEnabledDisabledValue';
        }
        // annotations that has custom mapping function
        $this->fixingMap['covers'] = 'fixCovers';
        $this->fixingMap['dataProvider'] = 'fixDataProvider';
        $this->fixingMap['depends'] = 'fixDepends';
        $this->fixingMap['requires'] = 'fixRequires';
        $this->fixingMap['testWith'] = 'fixTestWith';
        $this->fixingMap['uses'] = 'fixUses';
    }
    private static function shouldBeFixed(string $annotationName, string $annotationScope) : bool
    {
        if ('method' === $annotationScope && \in_array($annotationName, ['covers', 'large', 'medium', 'runTestsInSeparateProcesses', 'small', 'uses'], \true)) {
            return \false;
        }
        if ('class' === $annotationScope && \in_array($annotationName, ['after', 'afterClass', 'before', 'beforeClass', 'dataProvider', 'depends', 'postCondition', 'preCondition', 'runInSeparateProcess', 'test', 'testWith'], \true)) {
            return \false;
        }
        return \true;
    }
    /**
     * @param list<Token> $tokensToInsert
     */
    private static function isAttributeAlreadyPresent(Tokens $tokens, int $index, array $tokensToInsert) : bool
    {
        $attributeIndex = $tokens->getNextMeaningfulToken($index);
        if (!$tokens[$attributeIndex]->isGivenKind(\T_ATTRIBUTE)) {
            return \false;
        }
        $insertedClassName = '';
        foreach (\array_slice($tokensToInsert, 3) as $token) {
            if ($token->equals('(') || $token->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                break;
            }
            $insertedClassName .= $token->getContent();
        }
        // @TODO: refactor OrderedAttributesFixer::determineAttributeFullyQualifiedName to shared analyzer
        static $determineAttributeFullyQualifiedName = null;
        static $orderedAttributesFixer = null;
        if (null === $determineAttributeFullyQualifiedName) {
            $orderedAttributesFixer = new OrderedAttributesFixer();
            $reflection = new \ReflectionObject($orderedAttributesFixer);
            $determineAttributeFullyQualifiedName = $reflection->getMethod('determineAttributeFullyQualifiedName');
            $determineAttributeFullyQualifiedName->setAccessible(\true);
        }
        foreach (AttributeAnalyzer::collect($tokens, $attributeIndex) as $attributeAnalysis) {
            foreach ($attributeAnalysis->getAttributes() as $attribute) {
                $className = \ltrim($determineAttributeFullyQualifiedName->invokeArgs($orderedAttributesFixer, [$tokens, $attribute['name'], $attribute['start']]), '\\');
                if ($insertedClassName === $className) {
                    return \true;
                }
            }
        }
        return \false;
    }
    /**
     * @return list<Token>
     */
    private static function fixWithoutParameters(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        return self::createAttributeTokens($tokens, $index, self::getAttributeNameForAnnotation($annotation));
    }
    /**
     * @return list<Token>
     */
    private static function fixWithSingleStringValue(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        Preg::match(\sprintf('/@%s\\s+(.*\\S)(?:\\R|\\s*\\*+\\/$)/', $annotation->getTag()->getName()), $annotation->getContent(), $matches);
        if (!isset($matches[1])) {
            return [];
        }
        return self::createAttributeTokens($tokens, $index, self::getAttributeNameForAnnotation($annotation), self::createEscapedStringToken($matches[1]));
    }
    /**
     * @return list<Token>
     */
    private static function fixWithEnabledDisabledValue(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        $matches = self::getMatches($annotation);
        if (!isset($matches[1])) {
            return [];
        }
        return self::createAttributeTokens($tokens, $index, self::getAttributeNameForAnnotation($annotation), new Token([\T_STRING, isset($matches[1]) && 'enabled' === $matches[1] ? 'true' : 'false']));
    }
    /**
     * @return list<Token>
     */
    private static function fixCovers(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        $matches = self::getMatches($annotation);
        \assert(isset($matches[1]));
        if (\strncmp($matches[1], '::', \strlen('::')) === 0) {
            return self::createAttributeTokens($tokens, $index, 'CoversFunction', self::createEscapedStringToken(\substr($matches[1], 2)));
        }
        if (\strpos($matches[1], '::') === \false) {
            return self::createAttributeTokens($tokens, $index, 'CoversClass', ...self::toClassConstant($matches[1]));
        }
        return [];
    }
    /**
     * @return list<Token>
     */
    private static function fixDataProvider(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        $matches = self::getMatches($annotation);
        if (!isset($matches[1])) {
            return [];
        }
        if (\strpos($matches[1], '::') !== \false) {
            // @phpstan-ignore offsetAccess.notFound
            [$class, $method] = \explode('::', $matches[1]);
            return self::createAttributeTokens($tokens, $index, 'DataProviderExternal', ...\array_merge(self::toClassConstant($class), [new Token(','), new Token([\T_WHITESPACE, ' ']), self::createEscapedStringToken($method)]));
        }
        return self::createAttributeTokens($tokens, $index, 'DataProvider', self::createEscapedStringToken($matches[1]));
    }
    /**
     * @return list<Token>
     */
    private static function fixDepends(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        $matches = self::getMatches($annotation);
        if (!isset($matches[1])) {
            return [];
        }
        $nameSuffix = '';
        $depended = $matches[1];
        if (isset($matches[2])) {
            if ('clone' === $matches[1]) {
                $nameSuffix = 'UsingDeepClone';
                $depended = $matches[2];
            } elseif ('shallowClone' === $matches[1]) {
                $nameSuffix = 'UsingShallowClone';
                $depended = $matches[2];
            }
        }
        $class = null;
        $method = $depended;
        if (\strpos($depended, '::') !== \false) {
            // @phpstan-ignore offsetAccess.notFound
            [$class, $method] = \explode('::', $depended);
            if ('class' === $method) {
                $method = null;
                $nameSuffix = '' === $nameSuffix ? 'OnClass' : 'OnClass' . $nameSuffix;
            } else {
                $nameSuffix = 'External' . $nameSuffix;
            }
        }
        $attributeTokens = [];
        if (null !== $class) {
            $attributeTokens = self::toClassConstant($class);
        }
        if (null !== $method) {
            if ([] !== $attributeTokens) {
                $attributeTokens[] = new Token(',');
                $attributeTokens[] = new Token([\T_WHITESPACE, ' ']);
            }
            $attributeTokens[] = self::createEscapedStringToken($method);
        }
        return self::createAttributeTokens($tokens, $index, 'Depends' . $nameSuffix, ...$attributeTokens);
    }
    /**
     * @return list<Token>
     */
    private static function fixRequires(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        $matches = self::getMatches($annotation);
        if (!isset($matches[1])) {
            return [];
        }
        $map = ['extension' => 'RequiresPhpExtension', 'function' => 'RequiresFunction', 'PHP' => 'RequiresPhp', 'PHPUnit' => 'RequiresPhpunit', 'OS' => 'RequiresOperatingSystem', 'OSFAMILY' => 'RequiresOperatingSystemFamily', 'setting' => 'RequiresSetting'];
        if (!isset($matches[2]) || !isset($map[$matches[1]])) {
            return [];
        }
        $attributeName = $map[$matches[1]];
        if ('RequiresFunction' === $attributeName && \strpos($matches[2], '::') !== \false) {
            // @phpstan-ignore offsetAccess.notFound
            [$class, $method] = \explode('::', $matches[2]);
            $attributeName = 'RequiresMethod';
            $attributeTokens = \array_merge(self::toClassConstant($class), [new Token(','), new Token([\T_WHITESPACE, ' ']), self::createEscapedStringToken($method)]);
        } elseif ('RequiresPhp' === $attributeName && isset($matches[3])) {
            $attributeTokens = [self::createEscapedStringToken($matches[2] . ' ' . $matches[3])];
        } else {
            $attributeTokens = [self::createEscapedStringToken($matches[2])];
        }
        if (isset($matches[3]) && 'RequiresPhp' !== $attributeName) {
            $attributeTokens[] = new Token(',');
            $attributeTokens[] = new Token([\T_WHITESPACE, ' ']);
            $attributeTokens[] = self::createEscapedStringToken($matches[3]);
        }
        return self::createAttributeTokens($tokens, $index, $attributeName, ...$attributeTokens);
    }
    /**
     * @return list<Token>
     */
    private static function fixTestWith(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        $content = $annotation->getContent();
        $content = Preg::replace('/@testWith\\s+/', '', $content);
        $content = Preg::replace('/(^|\\R)\\s+\\**\\s*/', "\n", $content);
        $content = \trim($content);
        if ('' === $content) {
            return [];
        }
        $attributeTokens = [];
        foreach (\explode("\n", $content) as $json) {
            $attributeTokens = \array_merge($attributeTokens, self::createAttributeTokens($tokens, $index, 'TestWithJson', self::createEscapedStringToken($json)));
        }
        return $attributeTokens;
    }
    /**
     * @return list<Token>
     */
    private static function fixUses(Tokens $tokens, int $index, Annotation $annotation) : array
    {
        $matches = self::getMatches($annotation);
        if (!isset($matches[1])) {
            return [];
        }
        if (\strncmp($matches[1], '::', \strlen('::')) === 0) {
            $attributeName = 'UsesFunction';
            $attributeTokens = [self::createEscapedStringToken(\substr($matches[1], 2))];
        } elseif (Preg::match('/^[a-zA-Z\\d\\\\]+$/', $matches[1])) {
            $attributeName = 'UsesClass';
            $attributeTokens = self::toClassConstant($matches[1]);
        } else {
            return [];
        }
        return self::createAttributeTokens($tokens, $index, $attributeName, ...$attributeTokens);
    }
    /**
     * @return list<string>
     */
    private static function getMatches(Annotation $annotation) : array
    {
        Preg::match(\sprintf('/@%s\\s+(\\S+)(?:\\s+(\\S+))?(?:\\s+(.+\\S))?\\s*(?:\\R|\\*+\\/$)/', $annotation->getTag()->getName()), $annotation->getContent(), $matches);
        $arrayIsListFunction = function (array $array) : bool {
            if (\function_exists('array_is_list')) {
                return \array_is_list($array);
            }
            if ($array === []) {
                return \true;
            }
            $current_key = 0;
            foreach ($array as $key => $noop) {
                if ($key !== $current_key) {
                    return \false;
                }
                ++$current_key;
            }
            return \true;
        };
        \assert($arrayIsListFunction($matches));
        // preg_match matches is not well typed, it depends on used regex, let's assure the type to instruct SCA
        return $matches;
    }
    private static function getAttributeNameForAnnotation(Annotation $annotation) : string
    {
        $annotationName = $annotation->getTag()->getName();
        return 'backupStaticAttributes' === $annotationName ? 'BackupStaticProperties' : \ucfirst($annotationName);
    }
    /**
     * @return list<Token>
     */
    private static function createAttributeTokens(Tokens $tokens, int $index, string $className, Token ...$attributeTokens) : array
    {
        if ([] !== $attributeTokens) {
            $attributeTokens = \array_merge([new Token('(')], $attributeTokens, [new Token(')')]);
        }
        return \array_merge([clone $tokens[$index + 1], new Token([\T_ATTRIBUTE, '#[']), new Token([\T_NS_SEPARATOR, '\\']), new Token([\T_STRING, 'PHPUnit']), new Token([\T_NS_SEPARATOR, '\\']), new Token([\T_STRING, 'Framework']), new Token([\T_NS_SEPARATOR, '\\']), new Token([\T_STRING, 'Attributes']), new Token([\T_NS_SEPARATOR, '\\']), new Token([\T_STRING, $className])], $attributeTokens, [new Token([CT::T_ATTRIBUTE_CLOSE, ']'])]);
    }
    /**
     * @param class-string $name
     *
     * @return list<Token>
     */
    private static function toClassConstant(string $name) : array
    {
        return \array_merge(ImportProcessor::tokenizeName($name), [new Token([\T_DOUBLE_COLON, '::']), new Token([CT::T_CLASS_CONSTANT, 'class'])]);
    }
    private static function createEscapedStringToken(string $value) : Token
    {
        return new Token([\T_CONSTANT_ENCAPSED_STRING, "'" . \str_replace("'", "\\'", $value) . "'"]);
    }
}
