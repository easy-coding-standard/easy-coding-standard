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
namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class NativeFunctionTypeDeclarationCasingFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * https://secure.php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration.
     *
     * self     PHP 5.0
     * array    PHP 5.1
     * callable PHP 5.4
     * bool     PHP 7.0
     * float    PHP 7.0
     * int      PHP 7.0
     * string   PHP 7.0
     * iterable PHP 7.1
     * void     PHP 7.1
     * object   PHP 7.2
     * static   PHP 8.0 (return type only)
     * mixed    PHP 8.0
     *
     * @var array<string, true>
     */
    private $hints;
    /**
     * @var FunctionsAnalyzer
     */
    private $functionsAnalyzer;
    public function __construct()
    {
        parent::__construct();
        $this->hints = ['array' => \true, 'callable' => \true, 'self' => \true];
        if (\PHP_VERSION_ID >= 70000) {
            $this->hints = \array_merge($this->hints, ['bool' => \true, 'float' => \true, 'int' => \true, 'string' => \true]);
        }
        if (\PHP_VERSION_ID >= 70100) {
            $this->hints = \array_merge($this->hints, ['iterable' => \true, 'void' => \true]);
        }
        if (\PHP_VERSION_ID >= 70200) {
            $this->hints = \array_merge($this->hints, ['object' => \true]);
        }
        if (\PHP_VERSION_ID >= 80000) {
            $this->hints = \array_merge($this->hints, ['static' => \true]);
            $this->hints = \array_merge($this->hints, ['mixed' => \true]);
        }
        $this->functionsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Native type hints for functions should use the correct case.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nclass Bar {\n    public function Foo(CALLABLE \$bar)\n    {\n        return 1;\n    }\n}\n"), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction Foo(INT \$a): Bool\n{\n    return true;\n}\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(70000)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction Foo(Iterable \$a): VOID\n{\n    echo 'Hello world';\n}\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(70100)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction Foo(Object \$a)\n{\n    return 'hi!';\n}\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(70200))]);
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    public function isCandidate($tokens) : bool
    {
        return $tokens->isAllTokenKindsFound([\T_FUNCTION, \T_STRING]);
    }
    /**
     * {@inheritdoc}
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return void
     */
    protected function applyFix($file, $tokens)
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->isGivenKind(\T_FUNCTION)) {
                if (\PHP_VERSION_ID >= 70000) {
                    $this->fixFunctionReturnType($tokens, $index);
                }
                $this->fixFunctionArgumentTypes($tokens, $index);
            }
        }
    }
    /**
     * @return void
     */
    private function fixFunctionArgumentTypes(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index)
    {
        foreach ($this->functionsAnalyzer->getFunctionArguments($tokens, $index) as $argument) {
            $this->fixArgumentType($tokens, $argument->getTypeAnalysis());
        }
    }
    /**
     * @return void
     */
    private function fixFunctionReturnType(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index)
    {
        $this->fixArgumentType($tokens, $this->functionsAnalyzer->getFunctionReturnType($tokens, $index));
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis|null $type
     * @return void
     */
    private function fixArgumentType(\PhpCsFixer\Tokenizer\Tokens $tokens, $type = null)
    {
        if (null === $type) {
            return;
        }
        $argumentStartIndex = $type->getStartIndex();
        $argumentExpectedEndIndex = $type->isNullable() ? $tokens->getNextMeaningfulToken($argumentStartIndex) : $argumentStartIndex;
        if ($argumentExpectedEndIndex !== $type->getEndIndex()) {
            return;
            // the type to fix is always unqualified and so is always composed of one token and possible a nullable '?' one
        }
        $lowerCasedName = \strtolower($type->getName());
        if (!isset($this->hints[$lowerCasedName])) {
            return;
            // check of type is of interest based on name (slower check than previous index based)
        }
        $tokens[$argumentExpectedEndIndex] = new \PhpCsFixer\Tokenizer\Token([$tokens[$argumentExpectedEndIndex]->getId(), $lowerCasedName]);
    }
}
