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
namespace PhpCsFixer\FixerDefinition;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class CodeSample implements \PhpCsFixer\FixerDefinition\CodeSampleInterface
{
    /**
     * @var string
     */
    private $code;
    /**
     * @var null|array
     */
    private $configuration;
    /**
     * @param mixed[]|null $configuration
     */
    public function __construct(string $code, $configuration = null)
    {
        $this->code = $code;
        $this->configuration = $configuration;
    }
    public function getCode() : string
    {
        return $this->code;
    }
    /**
     * @return mixed[]|null
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
