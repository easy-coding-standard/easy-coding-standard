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
 * @author Andreas Möller <am@localheinz.com>
 */
final class VersionSpecificCodeSample implements \PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface
{
    /**
     * @var CodeSampleInterface
     */
    private $codeSample;
    /**
     * @var VersionSpecificationInterface
     */
    private $versionSpecification;
    /**
     * @param mixed[]|null $configuration
     * @param string $code
     * @param \PhpCsFixer\FixerDefinition\VersionSpecificationInterface $versionSpecification
     */
    public function __construct($code, $versionSpecification, $configuration = null)
    {
        $this->codeSample = new \PhpCsFixer\FixerDefinition\CodeSample($code, $configuration);
        $this->versionSpecification = $versionSpecification;
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
     * @param int $version
     * @return bool
     */
    public function isSuitableFor($version)
    {
        return $this->versionSpecification->isSatisfiedBy($version);
    }
}
