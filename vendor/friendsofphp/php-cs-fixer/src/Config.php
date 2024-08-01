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
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Config implements \PhpCsFixer\ConfigInterface, \PhpCsFixer\ParallelAwareConfigInterface
{
    /**
     * @var string
     */
    private $cacheFile = '.php-cs-fixer.cache';
    /**
     * @var list<FixerInterface>
     */
    private $customFixers = [];
    /**
     * @var null|iterable<\SplFileInfo>
     */
    private $finder;
    /**
     * @var string
     */
    private $format = 'txt';
    /**
     * @var bool
     */
    private $hideProgress = \false;
    /**
     * @var string
     */
    private $indent = '    ';
    /**
     * @var bool
     */
    private $isRiskyAllowed = \false;
    /**
     * @var string
     */
    private $lineEnding = "\n";
    /**
     * @var string
     */
    private $name;
    /**
     * @var \PhpCsFixer\Runner\Parallel\ParallelConfig
     */
    private $parallelConfig;
    /**
     * @var null|string
     */
    private $phpExecutable;
    /**
     * @TODO: 4.0 - update to @PER
     *
     * @var array<string, array<string, mixed>|bool>
     */
    private $rules;
    /**
     * @var bool
     */
    private $usingCache = \true;
    public function __construct(string $name = 'default')
    {
        // @TODO 4.0 cleanup
        if (\PhpCsFixer\Utils::isFutureModeEnabled()) {
            $this->name = $name . ' (future mode)';
            $this->rules = ['@PER-CS' => \true];
        } else {
            $this->name = $name;
            $this->rules = ['@PSR12' => \true];
        }
        // @TODO 4.0 cleanup
        if (\PhpCsFixer\Utils::isFutureModeEnabled() || \filter_var(\getenv('PHP_CS_FIXER_PARALLEL'), \FILTER_VALIDATE_BOOL)) {
            $this->parallelConfig = ParallelConfigFactory::detect();
        } else {
            $this->parallelConfig = ParallelConfigFactory::sequential();
        }
    }
    public function getCacheFile() : string
    {
        return $this->cacheFile;
    }
    public function getCustomFixers() : array
    {
        return $this->customFixers;
    }
    /**
     * @return Finder
     */
    public function getFinder() : iterable
    {
        $this->finder = $this->finder ?? new \PhpCsFixer\Finder();
        return $this->finder;
    }
    public function getFormat() : string
    {
        return $this->format;
    }
    public function getHideProgress() : bool
    {
        return $this->hideProgress;
    }
    public function getIndent() : string
    {
        return $this->indent;
    }
    public function getLineEnding() : string
    {
        return $this->lineEnding;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getParallelConfig() : ParallelConfig
    {
        return $this->parallelConfig;
    }
    public function getPhpExecutable() : ?string
    {
        return $this->phpExecutable;
    }
    public function getRiskyAllowed() : bool
    {
        return $this->isRiskyAllowed;
    }
    public function getRules() : array
    {
        return $this->rules;
    }
    public function getUsingCache() : bool
    {
        return $this->usingCache;
    }
    public function registerCustomFixers(iterable $fixers) : \PhpCsFixer\ConfigInterface
    {
        foreach ($fixers as $fixer) {
            $this->addCustomFixer($fixer);
        }
        return $this;
    }
    public function setCacheFile(string $cacheFile) : \PhpCsFixer\ConfigInterface
    {
        $this->cacheFile = $cacheFile;
        return $this;
    }
    public function setFinder(iterable $finder) : \PhpCsFixer\ConfigInterface
    {
        $this->finder = $finder;
        return $this;
    }
    public function setFormat(string $format) : \PhpCsFixer\ConfigInterface
    {
        $this->format = $format;
        return $this;
    }
    public function setHideProgress(bool $hideProgress) : \PhpCsFixer\ConfigInterface
    {
        $this->hideProgress = $hideProgress;
        return $this;
    }
    public function setIndent(string $indent) : \PhpCsFixer\ConfigInterface
    {
        $this->indent = $indent;
        return $this;
    }
    public function setLineEnding(string $lineEnding) : \PhpCsFixer\ConfigInterface
    {
        $this->lineEnding = $lineEnding;
        return $this;
    }
    public function setParallelConfig(ParallelConfig $config) : \PhpCsFixer\ConfigInterface
    {
        $this->parallelConfig = $config;
        return $this;
    }
    public function setPhpExecutable(?string $phpExecutable) : \PhpCsFixer\ConfigInterface
    {
        $this->phpExecutable = $phpExecutable;
        return $this;
    }
    public function setRiskyAllowed(bool $isRiskyAllowed) : \PhpCsFixer\ConfigInterface
    {
        $this->isRiskyAllowed = $isRiskyAllowed;
        return $this;
    }
    public function setRules(array $rules) : \PhpCsFixer\ConfigInterface
    {
        $this->rules = $rules;
        return $this;
    }
    public function setUsingCache(bool $usingCache) : \PhpCsFixer\ConfigInterface
    {
        $this->usingCache = $usingCache;
        return $this;
    }
    private function addCustomFixer(FixerInterface $fixer) : void
    {
        $this->customFixers[] = $fixer;
    }
}
