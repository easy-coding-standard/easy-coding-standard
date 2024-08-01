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
final class TypeAnalysis implements \PhpCsFixer\Tokenizer\Analyzer\Analysis\StartEndTokenAwareAnalysis
{
    /**
     * This list contains soft and hard reserved types that can be used or will be used by PHP at some point.
     *
     * More info:
     *
     * @see https://php.net/manual/en/functions.arguments.php#functions.arguments.type-declaration.types
     * @see https://php.net/manual/en/reserved.other-reserved-words.php
     *
     * @var list<string>
     */
    private static $reservedTypes = ['array', 'bool', 'callable', 'false', 'float', 'int', 'iterable', 'list', 'mixed', 'never', 'null', 'object', 'parent', 'resource', 'self', 'static', 'string', 'true', 'void'];
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $startIndex;
    /**
     * @var int
     */
    private $endIndex;
    /**
     * @var bool
     */
    private $nullable = \false;
    /**
     * @param ($startIndex is null ? null : int) $endIndex
     */
    public function __construct(string $name, ?int $startIndex = null, ?int $endIndex = null)
    {
        $this->name = $name;
        if (\strncmp($name, '?', \strlen('?')) === 0) {
            $this->name = \substr($name, 1);
            $this->nullable = \true;
        } elseif (\PHP_VERSION_ID >= 80000) {
            $this->nullable = \in_array('null', \array_map('trim', \explode('|', \strtolower($name))), \true);
        }
        if (null !== $startIndex) {
            $this->startIndex = $startIndex;
            $this->endIndex = $endIndex;
        }
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getStartIndex() : int
    {
        return $this->startIndex;
    }
    public function getEndIndex() : int
    {
        return $this->endIndex;
    }
    public function isReservedType() : bool
    {
        return \in_array(\strtolower($this->name), self::$reservedTypes, \true);
    }
    public function isNullable() : bool
    {
        return $this->nullable;
    }
}
