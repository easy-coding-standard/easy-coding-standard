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
namespace PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Config implements \PhpCsFixer\ConfigInterface
{
    private $cacheFile = '.php-cs-fixer.cache';
    private $customFixers = [];
    private $finder;
    private $format = 'txt';
    private $hideProgress = \false;
    private $indent = '    ';
    private $isRiskyAllowed = \false;
    private $lineEnding = "\n";
    private $name;
    private $phpExecutable;
    private $rules = ['@PSR12' => \true];
    private $usingCache = \true;
    /**
     * @param string $name
     */
    public function __construct($name = 'default')
    {
        $this->name = $name;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getCacheFile()
    {
        return $this->cacheFile;
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function getCustomFixers()
    {
        return $this->customFixers;
    }
    /**
     * @return mixed[]
     */
    public function getFinder()
    {
        if (null === $this->finder) {
            $this->finder = new \PhpCsFixer\Finder();
        }
        return $this->finder;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function getHideProgress()
    {
        return $this->hideProgress;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getLineEnding()
    {
        return $this->lineEnding;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * {@inheritdoc}
     * @return string|null
     */
    public function getPhpExecutable()
    {
        return $this->phpExecutable;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function getRiskyAllowed()
    {
        return $this->isRiskyAllowed;
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function getRules()
    {
        return $this->rules;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function getUsingCache()
    {
        return $this->usingCache;
    }
    /**
     * {@inheritdoc}
     * @param mixed[] $fixers
     * @return \PhpCsFixer\ConfigInterface
     */
    public function registerCustomFixers($fixers)
    {
        foreach ($fixers as $fixer) {
            $this->addCustomFixer($fixer);
        }
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param string $cacheFile
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setCacheFile($cacheFile)
    {
        $this->cacheFile = $cacheFile;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param mixed[] $finder
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setFinder($finder)
    {
        $this->finder = $finder;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param string $format
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param bool $hideProgress
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setHideProgress($hideProgress)
    {
        $this->hideProgress = $hideProgress;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param string $indent
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setIndent($indent)
    {
        $this->indent = $indent;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param string $lineEnding
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setLineEnding($lineEnding)
    {
        $this->lineEnding = $lineEnding;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param string|null $phpExecutable
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setPhpExecutable($phpExecutable)
    {
        $this->phpExecutable = $phpExecutable;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param bool $isRiskyAllowed
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setRiskyAllowed($isRiskyAllowed)
    {
        $this->isRiskyAllowed = $isRiskyAllowed;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
        return $this;
    }
    /**
     * {@inheritdoc}
     * @param bool $usingCache
     * @return \PhpCsFixer\ConfigInterface
     */
    public function setUsingCache($usingCache)
    {
        $this->usingCache = $usingCache;
        return $this;
    }
    /**
     * @return void
     * @param \PhpCsFixer\Fixer\FixerInterface $fixer
     */
    private function addCustomFixer($fixer)
    {
        $this->customFixers[] = $fixer;
    }
}
