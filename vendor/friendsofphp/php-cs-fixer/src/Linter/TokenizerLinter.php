<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Linter;

use PhpCsFixer\FileReader;
use PhpCsFixer\Tokenizer\CodeHasher;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * Handle PHP code linting.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TokenizerLinter implements \PhpCsFixer\Linter\LinterInterface
{
    public function __construct()
    {
        if (\false === \class_exists(\CompileError::class)) {
            throw new \PhpCsFixer\Linter\UnavailableLinterException('Cannot use tokenizer as linter.');
        }
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isAsync()
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     * @param string $path
     * @return \PhpCsFixer\Linter\LintingResultInterface
     */
    public function lintFile($path)
    {
        if (\is_object($path)) {
            $path = (string) $path;
        }
        return $this->lintSource(\PhpCsFixer\FileReader::createSingleton()->read($path));
    }
    /**
     * {@inheritdoc}
     * @param string $source
     * @return \PhpCsFixer\Linter\LintingResultInterface
     */
    public function lintSource($source)
    {
        if (\is_object($source)) {
            $source = (string) $source;
        }
        try {
            // To lint, we will parse the source into Tokens.
            // During that process, it might throw a ParseError or CompileError.
            // If it won't, cache of tokenized version of source will be kept, which is great for Runner.
            // Yet, first we need to clear already existing cache to not hit it and lint the code indeed.
            $codeHash = \PhpCsFixer\Tokenizer\CodeHasher::calculateCodeHash($source);
            \PhpCsFixer\Tokenizer\Tokens::clearCache($codeHash);
            \PhpCsFixer\Tokenizer\Tokens::fromCode($source);
            return new \PhpCsFixer\Linter\TokenizerLintingResult();
        } catch (\ParseError $e) {
            return new \PhpCsFixer\Linter\TokenizerLintingResult($e);
        } catch (\CompileError $e) {
            return new \PhpCsFixer\Linter\TokenizerLintingResult($e);
        }
    }
}
