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
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface ConfigInterface
{
    /**
     * Returns the path to the cache file.
     *
     * @return null|string Returns null if not using cache
     */
    public function getCacheFile();
    /**
     * Returns the custom fixers to use.
     *
     * @return FixerInterface[]
     */
    public function getCustomFixers() : array;
    /**
     * Returns files to scan.
     *
     * @return mixed[]
     */
    public function getFinder();
    public function getFormat() : string;
    /**
     * Returns true if progress should be hidden.
     */
    public function getHideProgress() : bool;
    public function getIndent() : string;
    public function getLineEnding() : string;
    /**
     * Returns the name of the configuration.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the configuration
     */
    public function getName() : string;
    /**
     * Get configured PHP executable, if any.
     * @return string|null
     */
    public function getPhpExecutable();
    /**
     * Check if it is allowed to run risky fixers.
     */
    public function getRiskyAllowed() : bool;
    /**
     * Get rules.
     *
     * Keys of array are names of fixers/sets, values are true/false.
     */
    public function getRules() : array;
    /**
     * Returns true if caching should be enabled.
     */
    public function getUsingCache() : bool;
    /**
     * Adds a suite of custom fixers.
     *
     * Name of custom fixer should follow `VendorName/rule_name` convention.
     *
     * @param FixerInterface[]|iterable|\Traversable $fixers
     * @return $this
     */
    public function registerCustomFixers($fixers);
    /**
     * Sets the path to the cache file.
     * @return $this
     */
    public function setCacheFile(string $cacheFile);
    /**
     * @return $this
     * @param mixed[] $finder
     */
    public function setFinder($finder);
    /**
     * @return $this
     */
    public function setFormat(string $format);
    /**
     * @return $this
     */
    public function setHideProgress(bool $hideProgress);
    /**
     * @return $this
     */
    public function setIndent(string $indent);
    /**
     * @return $this
     */
    public function setLineEnding(string $lineEnding);
    /**
     * Set PHP executable.
     * @return $this
     * @param string|null $phpExecutable
     */
    public function setPhpExecutable($phpExecutable);
    /**
     * Set if it is allowed to run risky fixers.
     * @return $this
     */
    public function setRiskyAllowed(bool $isRiskyAllowed);
    /**
     * Set rules.
     *
     * Keys of array are names of fixers or sets.
     * Value for set must be bool (turn it on or off).
     * Value for fixer may be bool (turn it on or off) or array of configuration
     * (turn it on and contains configuration for FixerInterface::configure method).
     * @return $this
     */
    public function setRules(array $rules);
    /**
     * @return $this
     */
    public function setUsingCache(bool $usingCache);
}
