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
namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

/**
 * @internal
 */
final class SwitchAnalysis extends \PhpCsFixer\Tokenizer\Analyzer\Analysis\AbstractControlCaseStructuresAnalysis
{
    /**
     * @var list<CaseAnalysis>
     */
    private $cases;
    /**
     * @var \PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis|null
     */
    private $defaultAnalysis;
    /**
     * @param list<CaseAnalysis> $cases
     */
    public function __construct(int $index, int $open, int $close, array $cases, ?\PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis $defaultAnalysis)
    {
        parent::__construct($index, $open, $close);
        $this->cases = $cases;
        $this->defaultAnalysis = $defaultAnalysis;
    }
    /**
     * @return list<CaseAnalysis>
     */
    public function getCases() : array
    {
        return $this->cases;
    }
    public function getDefaultAnalysis() : ?\PhpCsFixer\Tokenizer\Analyzer\Analysis\DefaultAnalysis
    {
        return $this->defaultAnalysis;
    }
}
