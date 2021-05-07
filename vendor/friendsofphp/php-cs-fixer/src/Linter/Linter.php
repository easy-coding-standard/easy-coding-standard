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

/**
 * Handle PHP code linting process.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class Linter implements \PhpCsFixer\Linter\LinterInterface
{
    /**
     * @var LinterInterface
     */
    private $sublinter;
    /**
     * @param null|string $executable PHP executable, null for autodetection
     */
    public function __construct($executable = null)
    {
        try {
            $this->sublinter = new \PhpCsFixer\Linter\TokenizerLinter();
        } catch (\PhpCsFixer\Linter\UnavailableLinterException $e) {
            $this->sublinter = new \PhpCsFixer\Linter\ProcessLinter($executable);
        }
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isAsync()
    {
        return $this->sublinter->isAsync();
    }
    /**
     * {@inheritdoc}
     * @param string $path
     * @return \PhpCsFixer\Linter\LintingResultInterface
     */
    public function lintFile($path)
    {
        return $this->sublinter->lintFile($path);
    }
    /**
     * {@inheritdoc}
     * @param string $source
     * @return \PhpCsFixer\Linter\LintingResultInterface
     */
    public function lintSource($source)
    {
        return $this->sublinter->lintSource($source);
    }
}
