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
    public function __construct(\PhpCsFixer\FixerConfiguration\FixerOptionInterface $option, string $deprecationMessage)
    {
        $this->option = $option;
        $this->deprecationMessage = $deprecationMessage;
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return $this->option->getName();
    }
    /**
     * {@inheritdoc}
     */
    public function getDescription() : string
    {
        return $this->option->getDescription();
    }
    /**
     * {@inheritdoc}
     */
    public function hasDefault() : bool
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
    public function getDeprecationMessage() : string
    {
        return $this->deprecationMessage;
    }
}
