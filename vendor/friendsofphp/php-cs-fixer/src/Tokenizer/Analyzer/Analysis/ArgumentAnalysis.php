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
namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

/**
 * @internal
 */
final class ArgumentAnalysis
{
    /**
     * The default value of the argument.
     *
     * @var null|string
     */
    private $default;
    /**
     * The name of the argument.
     *
     * @var string
     */
    private $name;
    /**
     * The index where the name is located in the supplied Tokens object.
     *
     * @var int
     */
    private $nameIndex;
    /**
     * The type analysis of the argument.
     *
     * @var ?TypeAnalysis
     */
    private $typeAnalysis;
    /**
     * @param string|null $default
     * @param \PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis|null $typeAnalysis
     * @param string $name
     * @param int $nameIndex
     */
    public function __construct($name, $nameIndex, $default, $typeAnalysis = null)
    {
        $this->name = $name;
        $this->nameIndex = $nameIndex;
        $this->default = $default ?: null;
        $this->typeAnalysis = $typeAnalysis ?: null;
    }
    /**
     * @return string|null
     */
    public function getDefault()
    {
        return $this->default;
    }
    /**
     * @return bool
     */
    public function hasDefault()
    {
        return null !== $this->default;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @return int
     */
    public function getNameIndex()
    {
        return $this->nameIndex;
    }
    /**
     * @return \PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis|null
     */
    public function getTypeAnalysis()
    {
        return $this->typeAnalysis;
    }
    /**
     * @return bool
     */
    public function hasTypeAnalysis()
    {
        return null !== $this->typeAnalysis;
    }
}
