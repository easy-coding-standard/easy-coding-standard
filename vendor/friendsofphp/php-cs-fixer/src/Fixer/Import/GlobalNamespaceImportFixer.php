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
namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\ClassyAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class GlobalNamespaceImportFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface, \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Imports or fully qualifies global classes/functions/constants.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php

namespace Foo;

$d = new \\DateTimeImmutable();
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

namespace Foo;

if (\\count($x)) {
    /** @var \\DateTimeImmutable $d */
    $d = new \\DateTimeImmutable();
    $p = \\M_PI;
}
', ['import_classes' => \true, 'import_constants' => \true, 'import_functions' => \true]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

namespace Foo;

use DateTimeImmutable;
use function count;
use const M_PI;

if (count($x)) {
    /** @var DateTimeImmutable $d */
    $d = new DateTimeImmutable();
    $p = M_PI;
}
', ['import_classes' => \false, 'import_constants' => \false, 'import_functions' => \false])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoUnusedImportsFixer, OrderedImportsFixer.
     * Must run after NativeConstantInvocationFixer, NativeFunctionInvocationFixer.
     */
    public function getPriority() : int
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_DOC_COMMENT, \T_NS_SEPARATOR, \T_USE]) && $tokens->isTokenKindFound(\T_NAMESPACE) && 1 === $tokens->countTokenKind(\T_NAMESPACE) && $tokens->isMonolithicPhp();
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $namespaceAnalyses = (new \PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer())->getDeclarations($tokens);
        if (1 !== \count($namespaceAnalyses) || $namespaceAnalyses[0]->isGlobalNamespace()) {
            return;
        }
        $useDeclarations = (new \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens);
        $newImports = [];
        if (\true === $this->configuration['import_constants']) {
            $newImports['const'] = $this->importConstants($tokens, $useDeclarations);
        } elseif (\false === $this->configuration['import_constants']) {
            $this->fullyQualifyConstants($tokens, $useDeclarations);
        }
        if (\true === $this->configuration['import_functions']) {
            $newImports['function'] = $this->importFunctions($tokens, $useDeclarations);
        } elseif (\false === $this->configuration['import_functions']) {
            $this->fullyQualifyFunctions($tokens, $useDeclarations);
        }
        if (\true === $this->configuration['import_classes']) {
            $newImports['class'] = $this->importClasses($tokens, $useDeclarations);
        } elseif (\false === $this->configuration['import_classes']) {
            $this->fullyQualifyClasses($tokens, $useDeclarations);
        }
        $newImports = \array_filter($newImports);
        if (\count($newImports) > 0) {
            $this->insertImports($tokens, $newImports, $useDeclarations);
        }
    }
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('import_constants', 'Whether to import, not import or ignore global constants.'))->setDefault(null)->setAllowedValues([\true, \false, null])->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('import_functions', 'Whether to import, not import or ignore global functions.'))->setDefault(null)->setAllowedValues([\true, \false, null])->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('import_classes', 'Whether to import, not import or ignore global classes.'))->setDefault(\true)->setAllowedValues([\true, \false, null])->getOption()]);
    }
    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function importConstants(\PhpCsFixer\Tokenizer\Tokens $tokens, array $useDeclarations) : array
    {
        [$global, $other] = $this->filterUseDeclarations($useDeclarations, static function (\PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis $declaration) : bool {
            return $declaration->isConstant();
        }, \true);
        // find namespaced const declarations (`const FOO = 1`)
        // and add them to the not importable names (already used)
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            $token = $tokens[$index];
            if ($token->isClassy()) {
                $index = $tokens->getNextTokenOfKind($index, ['{']);
                $index = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                continue;
            }
            if (!$token->isGivenKind(\T_CONST)) {
                continue;
            }
            $index = $tokens->getNextMeaningfulToken($index);
            $other[$tokens[$index]->getContent()] = \true;
        }
        $analyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $indices = [];
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_STRING)) {
                continue;
            }
            $name = $token->getContent();
            if (isset($other[$name])) {
                continue;
            }
            if (!$analyzer->isConstantInvocation($index)) {
                continue;
            }
            $nsSeparatorIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$nsSeparatorIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                if (!isset($global[$name])) {
                    // found an unqualified constant invocation
                    // add it to the not importable names (already used)
                    $other[$name] = \true;
                }
                continue;
            }
            $prevIndex = $tokens->getPrevMeaningfulToken($nsSeparatorIndex);
            if ($tokens[$prevIndex]->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_NAMESPACE_OPERATOR, \T_STRING])) {
                continue;
            }
            $indices[] = $index;
        }
        return $this->prepareImports($tokens, $indices, $global, $other, \true);
    }
    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function importFunctions(\PhpCsFixer\Tokenizer\Tokens $tokens, array $useDeclarations) : array
    {
        [$global, $other] = $this->filterUseDeclarations($useDeclarations, static function (\PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis $declaration) : bool {
            return $declaration->isFunction();
        }, \false);
        // find function declarations
        // and add them to the not importable names (already used)
        foreach ($this->findFunctionDeclarations($tokens, 0, $tokens->count() - 1) as $name) {
            $other[\strtolower($name)] = \true;
        }
        $analyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        $indices = [];
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_STRING)) {
                continue;
            }
            $name = \strtolower($token->getContent());
            if (isset($other[$name])) {
                continue;
            }
            if (!$analyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }
            $nsSeparatorIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$nsSeparatorIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                if (!isset($global[$name])) {
                    $other[$name] = \true;
                }
                continue;
            }
            $indices[] = $index;
        }
        return $this->prepareImports($tokens, $indices, $global, $other, \false);
    }
    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function importClasses(\PhpCsFixer\Tokenizer\Tokens $tokens, array $useDeclarations) : array
    {
        [$global, $other] = $this->filterUseDeclarations($useDeclarations, static function (\PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis $declaration) : bool {
            return $declaration->isClass();
        }, \false);
        /** @var DocBlock[] $docBlocks */
        $docBlocks = [];
        // find class declarations and class usages in docblocks
        // and add them to the not importable names (already used)
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            $token = $tokens[$index];
            if ($token->isGivenKind(\T_DOC_COMMENT)) {
                $docBlocks[$index] = new \PhpCsFixer\DocBlock\DocBlock($token->getContent());
                $this->traverseDocBlockTypes($docBlocks[$index], static function (string $type) use($global, &$other) : void {
                    if (\strpos($type, '\\') !== \false) {
                        return;
                    }
                    $name = \strtolower($type);
                    if (!isset($global[$name])) {
                        $other[$name] = \true;
                    }
                });
            }
            if (!$token->isClassy()) {
                continue;
            }
            $index = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$index]->isGivenKind(\T_STRING)) {
                $other[\strtolower($tokens[$index]->getContent())] = \true;
            }
        }
        $analyzer = new \PhpCsFixer\Tokenizer\Analyzer\ClassyAnalyzer();
        $indices = [];
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_STRING)) {
                continue;
            }
            $name = \strtolower($token->getContent());
            if (isset($other[$name])) {
                continue;
            }
            if (!$analyzer->isClassyInvocation($tokens, $index)) {
                continue;
            }
            $nsSeparatorIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$nsSeparatorIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                if (!isset($global[$name])) {
                    $other[$name] = \true;
                }
                continue;
            }
            if ($tokens[$tokens->getPrevMeaningfulToken($nsSeparatorIndex)]->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_NAMESPACE_OPERATOR, \T_STRING])) {
                continue;
            }
            $indices[] = $index;
        }
        $imports = [];
        foreach ($docBlocks as $index => $docBlock) {
            $changed = $this->traverseDocBlockTypes($docBlock, static function (string $type) use($global, $other, &$imports) : string {
                if ('\\' !== $type[0]) {
                    return $type;
                }
                $name = \substr($type, 1);
                $checkName = \strtolower($name);
                if (\strpos($checkName, '\\') !== \false || isset($other[$checkName])) {
                    return $type;
                }
                if (isset($global[$checkName])) {
                    return \is_string($global[$checkName]) ? $global[$checkName] : $name;
                }
                $imports[$checkName] = $name;
                return $name;
            });
            if ($changed) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $docBlock->getContent()]);
            }
        }
        return $imports + $this->prepareImports($tokens, $indices, $global, $other, \false);
    }
    /**
     * Removes the leading slash at the given indices (when the name is not already used).
     *
     * @param int[] $indices
     *
     * @return array array keys contain the names that must be imported
     */
    private function prepareImports(\PhpCsFixer\Tokenizer\Tokens $tokens, array $indices, array $global, array $other, bool $caseSensitive) : array
    {
        $imports = [];
        foreach ($indices as $index) {
            $name = $tokens[$index]->getContent();
            $checkName = $caseSensitive ? $name : \strtolower($name);
            if (isset($other[$checkName])) {
                continue;
            }
            if (!isset($global[$checkName])) {
                $imports[$checkName] = $name;
            } elseif (\is_string($global[$checkName])) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $global[$checkName]]);
            }
            $tokens->clearAt($tokens->getPrevMeaningfulToken($index));
        }
        return $imports;
    }
    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function insertImports(\PhpCsFixer\Tokenizer\Tokens $tokens, array $imports, array $useDeclarations) : void
    {
        if (\count($useDeclarations) > 0) {
            $useDeclaration = \end($useDeclarations);
            $index = $useDeclaration->getEndIndex() + 1;
        } else {
            $namespace = (new \PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer())->getDeclarations($tokens)[0];
            $index = $namespace->getEndIndex() + 1;
        }
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        if (!$tokens[$index]->isWhitespace() || \strpos($tokens[$index]->getContent(), "\n") === \false) {
            $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $lineEnding]));
        }
        foreach ($imports as $type => $typeImports) {
            foreach ($typeImports as $name) {
                $items = [new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $lineEnding]), new \PhpCsFixer\Tokenizer\Token([\T_USE, 'use']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])];
                if ('const' === $type) {
                    $items[] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT, 'const']);
                    $items[] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
                } elseif ('function' === $type) {
                    $items[] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT, 'function']);
                    $items[] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
                }
                $items[] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $name]);
                $items[] = new \PhpCsFixer\Tokenizer\Token(';');
                $tokens->insertAt($index, $items);
            }
        }
    }
    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function fullyQualifyConstants(\PhpCsFixer\Tokenizer\Tokens $tokens, array $useDeclarations) : void
    {
        if (!$tokens->isTokenKindFound(\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT)) {
            return;
        }
        [$global] = $this->filterUseDeclarations($useDeclarations, static function (\PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis $declaration) : bool {
            return $declaration->isConstant() && !$declaration->isAliased();
        }, \true);
        if (!$global) {
            return;
        }
        $analyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_STRING)) {
                continue;
            }
            if (!isset($global[$token->getContent()])) {
                continue;
            }
            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(\T_NS_SEPARATOR)) {
                continue;
            }
            if (!$analyzer->isConstantInvocation($index)) {
                continue;
            }
            $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']));
        }
    }
    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function fullyQualifyFunctions(\PhpCsFixer\Tokenizer\Tokens $tokens, array $useDeclarations) : void
    {
        if (!$tokens->isTokenKindFound(\PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT)) {
            return;
        }
        [$global] = $this->filterUseDeclarations($useDeclarations, static function (\PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis $declaration) : bool {
            return $declaration->isFunction() && !$declaration->isAliased();
        }, \false);
        if (!$global) {
            return;
        }
        $analyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_STRING)) {
                continue;
            }
            if (!isset($global[\strtolower($token->getContent())])) {
                continue;
            }
            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(\T_NS_SEPARATOR)) {
                continue;
            }
            if (!$analyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }
            $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']));
        }
    }
    /**
     * @param NamespaceUseAnalysis[] $useDeclarations
     */
    private function fullyQualifyClasses(\PhpCsFixer\Tokenizer\Tokens $tokens, array $useDeclarations) : void
    {
        if (!$tokens->isTokenKindFound(\T_USE)) {
            return;
        }
        [$global] = $this->filterUseDeclarations($useDeclarations, static function (\PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis $declaration) : bool {
            return $declaration->isClass() && !$declaration->isAliased();
        }, \false);
        if (!$global) {
            return;
        }
        $analyzer = new \PhpCsFixer\Tokenizer\Analyzer\ClassyAnalyzer();
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if ($token->isGivenKind(\T_DOC_COMMENT)) {
                $doc = new \PhpCsFixer\DocBlock\DocBlock($token->getContent());
                $changed = $this->traverseDocBlockTypes($doc, static function (string $type) use($global) : string {
                    if (!isset($global[\strtolower($type)])) {
                        return $type;
                    }
                    return '\\' . $type;
                });
                if ($changed) {
                    $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $doc->getContent()]);
                }
                continue;
            }
            if (!$token->isGivenKind(\T_STRING)) {
                continue;
            }
            if (!isset($global[\strtolower($token->getContent())])) {
                continue;
            }
            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(\T_NS_SEPARATOR)) {
                continue;
            }
            if (!$analyzer->isClassyInvocation($tokens, $index)) {
                continue;
            }
            $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']));
        }
    }
    /**
     * @param NamespaceUseAnalysis[] $declarations
     */
    private function filterUseDeclarations(array $declarations, callable $callback, bool $caseSensitive) : array
    {
        $global = [];
        $other = [];
        foreach ($declarations as $declaration) {
            if (!$callback($declaration)) {
                continue;
            }
            $fullName = \ltrim($declaration->getFullName(), '\\');
            if (\strpos($fullName, '\\') !== \false) {
                $name = $caseSensitive ? $declaration->getShortName() : \strtolower($declaration->getShortName());
                $other[$name] = \true;
                continue;
            }
            $checkName = $caseSensitive ? $fullName : \strtolower($fullName);
            $alias = $declaration->getShortName();
            $global[$checkName] = $alias === $fullName ? \true : $alias;
        }
        return [$global, $other];
    }
    private function findFunctionDeclarations(\PhpCsFixer\Tokenizer\Tokens $tokens, int $start, int $end) : iterable
    {
        for ($index = $start; $index <= $end; ++$index) {
            $token = $tokens[$index];
            if ($token->isClassy()) {
                $classStart = $tokens->getNextTokenOfKind($index, ['{']);
                $classEnd = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $classStart);
                for ($index = $classStart; $index <= $classEnd; ++$index) {
                    if (!$tokens[$index]->isGivenKind(\T_FUNCTION)) {
                        continue;
                    }
                    $methodStart = $tokens->getNextTokenOfKind($index, ['{', ';']);
                    if ($tokens[$methodStart]->equals(';')) {
                        $index = $methodStart;
                        continue;
                    }
                    $methodEnd = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $methodStart);
                    foreach ($this->findFunctionDeclarations($tokens, $methodStart, $methodEnd) as $function) {
                        (yield $function);
                    }
                    $index = $methodEnd;
                }
                continue;
            }
            if (!$token->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            $index = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$index]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_RETURN_REF)) {
                $index = $tokens->getNextMeaningfulToken($index);
            }
            if ($tokens[$index]->isGivenKind(\T_STRING)) {
                (yield $tokens[$index]->getContent());
            }
        }
    }
    private function traverseDocBlockTypes(\PhpCsFixer\DocBlock\DocBlock $doc, callable $callback) : bool
    {
        $annotations = $doc->getAnnotationsOfType(\PhpCsFixer\DocBlock\Annotation::getTagsWithTypes());
        if (0 === \count($annotations)) {
            return \false;
        }
        $changed = \false;
        foreach ($annotations as $annotation) {
            $types = $new = $annotation->getTypes();
            foreach ($types as $i => $fullType) {
                $newFullType = $fullType;
                \PhpCsFixer\Preg::matchAll('/[\\\\\\w]+/', $fullType, $matches, \PREG_OFFSET_CAPTURE);
                foreach (\array_reverse($matches[0]) as [$type, $offset]) {
                    $newType = $callback($type);
                    if (null !== $newType && $type !== $newType) {
                        $newFullType = \substr_replace($newFullType, $newType, $offset, \strlen($type));
                    }
                }
                $new[$i] = $newFullType;
            }
            if ($types !== $new) {
                $annotation->setTypes($new);
                $changed = \true;
            }
        }
        return $changed;
    }
}
