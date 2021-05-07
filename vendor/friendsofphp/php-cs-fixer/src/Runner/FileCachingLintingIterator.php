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
namespace PhpCsFixer\Runner;

use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingResultInterface;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileCachingLintingIterator extends \CachingIterator
{
    /**
     * @var LintingResultInterface
     */
    private $currentResult;
    /**
     * @var LinterInterface
     */
    private $linter;
    /**
     * @var LintingResultInterface
     */
    private $nextResult;
    /**
     * @param \Iterator $iterator
     * @param \PhpCsFixer\Linter\LinterInterface $linter
     */
    public function __construct($iterator, $linter)
    {
        parent::__construct($iterator);
        $this->linter = $linter;
    }
    /**
     * @return \PhpCsFixer\Linter\LintingResultInterface
     */
    public function currentLintingResult()
    {
        return $this->currentResult;
    }
    /**
     * @return void
     */
    public function next()
    {
        parent::next();
        $this->currentResult = $this->nextResult;
        if ($this->hasNext()) {
            $this->nextResult = $this->handleItem($this->getInnerIterator()->current());
        }
    }
    /**
     * @return void
     */
    public function rewind()
    {
        parent::rewind();
        if ($this->valid()) {
            $this->currentResult = $this->handleItem($this->current());
        }
        if ($this->hasNext()) {
            $this->nextResult = $this->handleItem($this->getInnerIterator()->current());
        }
    }
    /**
     * @param \SplFileInfo $file
     * @return \PhpCsFixer\Linter\LintingResultInterface
     */
    private function handleItem($file)
    {
        return $this->linter->lintFile($file->getRealPath());
    }
}
