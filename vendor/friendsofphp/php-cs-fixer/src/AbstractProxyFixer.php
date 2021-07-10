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
namespace PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractProxyFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * @var array<string, FixerInterface>
     */
    protected $proxyFixers;
    public function __construct()
    {
        foreach (\PhpCsFixer\Utils::sortFixers($this->createProxyFixers()) as $proxyFixer) {
            $this->proxyFixers[$proxyFixer->getName()] = $proxyFixer;
        }
        parent::__construct();
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    public function isCandidate($tokens) : bool
    {
        foreach ($this->proxyFixers as $fixer) {
            if ($fixer->isCandidate($tokens)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky() : bool
    {
        foreach ($this->proxyFixers as $fixer) {
            if ($fixer->isRisky()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function getPriority() : int
    {
        if (\count($this->proxyFixers) > 1) {
            throw new \LogicException('You need to override this method to provide the priority of combined fixers.');
        }
        return \reset($this->proxyFixers)->getPriority();
    }
    /**
     * {@inheritdoc}
     * @param \SplFileInfo $file
     */
    public function supports($file) : bool
    {
        foreach ($this->proxyFixers as $fixer) {
            if ($fixer->supports($file)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\WhitespacesFixerConfig $config
     * @return void
     */
    public function setWhitespacesConfig($config)
    {
        parent::setWhitespacesConfig($config);
        foreach ($this->proxyFixers as $fixer) {
            if ($fixer instanceof \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface) {
                $fixer->setWhitespacesConfig($config);
            }
        }
    }
    /**
     * {@inheritdoc}
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return void
     */
    protected function applyFix($file, $tokens)
    {
        foreach ($this->proxyFixers as $fixer) {
            $fixer->fix($file, $tokens);
        }
    }
    /**
     * @return FixerInterface[]
     */
    protected abstract function createProxyFixers() : array;
}
