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
namespace PhpCsFixer\Linter;

use ECSPrefix20220522\Symfony\Component\Process\Process;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ProcessLinterProcessBuilder
{
    /**
     * @var string
     */
    private $executable;
    /**
     * @param string $executable PHP executable
     */
    public function __construct(string $executable)
    {
        $this->executable = $executable;
    }
    public function build(string $path) : \ECSPrefix20220522\Symfony\Component\Process\Process
    {
        return new \ECSPrefix20220522\Symfony\Component\Process\Process([$this->executable, '-l', $path]);
    }
}
