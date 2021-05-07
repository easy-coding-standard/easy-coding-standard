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
namespace PhpCsFixer\FixerConfiguration;

/**
 * @author ntzm
 *
 * @internal
 */
final class AliasedFixerOption implements \PhpCsFixer\FixerConfiguration\FixerOptionInterface
{
    /**
     * @var FixerOptionInterface
     */
    private $fixerOption;
    /**
     * @var string
     */
    private $alias;
    /**
     * @param \PhpCsFixer\FixerConfiguration\FixerOptionInterface $fixerOption
     * @param string $alias
     */
    public function __construct($fixerOption, $alias)
    {
        $this->fixerOption = $fixerOption;
        $this->alias = $alias;
    }
    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getName()
    {
        return $this->fixerOption->getName();
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getDescription()
    {
        return $this->fixerOption->getDescription();
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function hasDefault()
    {
        return $this->fixerOption->hasDefault();
    }
    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return $this->fixerOption->getDefault();
    }
    /**
     * {@inheritdoc}
     * @return mixed[]|null
     */
    public function getAllowedTypes()
    {
        return $this->fixerOption->getAllowedTypes();
    }
    /**
     * {@inheritdoc}
     * @return mixed[]|null
     */
    public function getAllowedValues()
    {
        return $this->fixerOption->getAllowedValues();
    }
    /**
     * {@inheritdoc}
     * @return \Closure|null
     */
    public function getNormalizer()
    {
        return $this->fixerOption->getNormalizer();
    }
}
