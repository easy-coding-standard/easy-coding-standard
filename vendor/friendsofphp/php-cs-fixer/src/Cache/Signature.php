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
namespace PhpCsFixer\Cache;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class Signature implements \PhpCsFixer\Cache\SignatureInterface
{
    /**
     * @var string
     */
    private $phpVersion;
    /**
     * @var string
     */
    private $fixerVersion;
    /**
     * @var string
     */
    private $indent;
    /**
     * @var string
     */
    private $lineEnding;
    /**
     * @var array
     */
    private $rules;
    /**
     * @param string $phpVersion
     * @param string $fixerVersion
     * @param string $indent
     * @param string $lineEnding
     */
    public function __construct($phpVersion, $fixerVersion, $indent, $lineEnding, array $rules)
    {
        $this->phpVersion = $phpVersion;
        $this->fixerVersion = $fixerVersion;
        $this->indent = $indent;
        $this->lineEnding = $lineEnding;
        $this->rules = self::utf8Encode($rules);
    }
    /**
     * @return string
     */
    public function getPhpVersion()
    {
        return $this->phpVersion;
    }
    /**
     * @return string
     */
    public function getFixerVersion()
    {
        return $this->fixerVersion;
    }
    /**
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }
    /**
     * @return string
     */
    public function getLineEnding()
    {
        return $this->lineEnding;
    }
    /**
     * @return mixed[]
     */
    public function getRules()
    {
        return $this->rules;
    }
    /**
     * @param \PhpCsFixer\Cache\SignatureInterface $signature
     * @return bool
     */
    public function equals($signature)
    {
        return $this->phpVersion === $signature->getPhpVersion() && $this->fixerVersion === $signature->getFixerVersion() && $this->indent === $signature->getIndent() && $this->lineEnding === $signature->getLineEnding() && $this->rules === $signature->getRules();
    }
    /**
     * @return mixed[]
     */
    private static function utf8Encode(array $data)
    {
        if (!\function_exists('mb_detect_encoding')) {
            return $data;
        }
        \array_walk_recursive($data, static function (&$item) {
            if (\is_string($item) && !\mb_detect_encoding($item, 'utf-8', \true)) {
                $item = \utf8_encode($item);
            }
        });
        return $data;
    }
}
