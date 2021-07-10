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
namespace PhpCsFixer\Linter;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class CachingLinter implements \PhpCsFixer\Linter\LinterInterface
{
    /**
     * @var LinterInterface
     */
    private $sublinter;
    /**
     * @var array<int, LintingResultInterface>
     */
    private $cache = [];
    public function __construct(\PhpCsFixer\Linter\LinterInterface $linter)
    {
        $this->sublinter = $linter;
    }
    /**
     * {@inheritdoc}
     */
    public function isAsync() : bool
    {
        return $this->sublinter->isAsync();
    }
    /**
     * {@inheritdoc}
     * @param string $path
     */
    public function lintFile($path) : \PhpCsFixer\Linter\LintingResultInterface
    {
        $checksum = \crc32(\file_get_contents($path));
        if (!isset($this->cache[$checksum])) {
            $this->cache[$checksum] = $this->sublinter->lintFile($path);
        }
        return $this->cache[$checksum];
    }
    /**
     * {@inheritdoc}
     * @param string $source
     */
    public function lintSource($source) : \PhpCsFixer\Linter\LintingResultInterface
    {
        $checksum = \crc32($source);
        if (!isset($this->cache[$checksum])) {
            $this->cache[$checksum] = $this->sublinter->lintSource($source);
        }
        return $this->cache[$checksum];
    }
}
