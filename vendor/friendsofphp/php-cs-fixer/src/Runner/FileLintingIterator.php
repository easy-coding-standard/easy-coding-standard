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
namespace PhpCsFixer\Runner;

use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingResultInterface;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileLintingIterator extends \IteratorIterator
{
    /**
     * @var LintingResultInterface
     */
    private $currentResult;
    /**
     * @var null|LinterInterface
     */
    private $linter;
    public function __construct(\Iterator $iterator, \PhpCsFixer\Linter\LinterInterface $linter)
    {
        parent::__construct($iterator);
        $this->linter = $linter;
    }
    public function currentLintingResult() : ?\PhpCsFixer\Linter\LintingResultInterface
    {
        return $this->currentResult;
    }
    public function next() : void
    {
        parent::next();
        $this->currentResult = $this->valid() ? $this->handleItem($this->current()) : null;
    }
    public function rewind() : void
    {
        parent::rewind();
        $this->currentResult = $this->valid() ? $this->handleItem($this->current()) : null;
    }
    private function handleItem(\SplFileInfo $file) : \PhpCsFixer\Linter\LintingResultInterface
    {
        return $this->linter->lintFile($file->getRealPath());
    }
}
