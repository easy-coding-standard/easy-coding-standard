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
namespace PhpCsFixer\Linter;

use ECSPrefix20210508\Symfony\Component\Process\Process;
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
    public function __construct($executable)
    {
        $executable = (string) $executable;
        $this->executable = $executable;
    }
    /**
     * @param string $path
     * @return \Symfony\Component\Process\Process
     */
    public function build($path)
    {
        $path = (string) $path;
        return new \ECSPrefix20210508\Symfony\Component\Process\Process([$this->executable, '-l', $path]);
    }
}
