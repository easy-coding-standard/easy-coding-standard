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

final class DeprecatedFixerOption implements \PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface
{
    /**
     * @var FixerOptionInterface
     */
    private $option;
    /**
     * @var string
     */
    private $deprecationMessage;
    /**
     * @param \PhpCsFixer\FixerConfiguration\FixerOptionInterface $option
     * @param string $deprecationMessage
     */
    public function __construct($option, $deprecationMessage)
    {
        $this->option = $option;
        $this->deprecationMessage = $deprecationMessage;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getName()
    {
        return $this->option->getName();
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getDescription()
    {
        return $this->option->getDescription();
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function hasDefault()
    {
        return $this->option->hasDefault();
    }
    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return $this->option->getDefault();
    }
    /**
     * {@inheritdoc}
     * @return mixed[]|null
     */
    public function getAllowedTypes()
    {
        return $this->option->getAllowedTypes();
    }
    /**
     * {@inheritdoc}
     * @return mixed[]|null
     */
    public function getAllowedValues()
    {
        return $this->option->getAllowedValues();
    }
    /**
     * {@inheritdoc}
     * @return \Closure|null
     */
    public function getNormalizer()
    {
        return $this->option->getNormalizer();
    }
    /**
     * @return string
     */
    public function getDeprecationMessage()
    {
        return $this->deprecationMessage;
    }
}
