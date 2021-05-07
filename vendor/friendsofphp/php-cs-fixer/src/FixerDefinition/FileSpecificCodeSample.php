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
namespace PhpCsFixer\FixerDefinition;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FileSpecificCodeSample implements \PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface
{
    /**
     * @var CodeSampleInterface
     */
    private $codeSample;
    /**
     * @var \SplFileInfo
     */
    private $splFileInfo;
    /**
     * @param mixed[]|null $configuration
     * @param string $code
     * @param \SplFileInfo $splFileInfo
     */
    public function __construct($code, $splFileInfo, $configuration = null)
    {
        $this->codeSample = new \PhpCsFixer\FixerDefinition\CodeSample($code, $configuration);
        $this->splFileInfo = $splFileInfo;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getCode()
    {
        return $this->codeSample->getCode();
    }
    /**
     * {@inheritdoc}
     * @return mixed[]|null
     */
    public function getConfiguration()
    {
        return $this->codeSample->getConfiguration();
    }
    /**
     * {@inheritdoc}
     * @return \SplFileInfo
     */
    public function getSplFileInfo()
    {
        return $this->splFileInfo;
    }
}
