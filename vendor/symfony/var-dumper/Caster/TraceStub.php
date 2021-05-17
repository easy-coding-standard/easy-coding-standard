<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210517\Symfony\Component\VarDumper\Caster;

use ECSPrefix20210517\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * Represents a backtrace as returned by debug_backtrace() or Exception->getTrace().
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class TraceStub extends \ECSPrefix20210517\Symfony\Component\VarDumper\Cloner\Stub
{
    public $keepArgs;
    public $sliceOffset;
    public $sliceLength;
    public $numberingOffset;
    /**
     * @param bool $keepArgs
     * @param int $sliceOffset
     * @param int $sliceLength
     * @param int $numberingOffset
     */
    public function __construct(array $trace, $keepArgs = \true, $sliceOffset = 0, $sliceLength = null, $numberingOffset = 0)
    {
        $keepArgs = (bool) $keepArgs;
        $sliceOffset = (int) $sliceOffset;
        $numberingOffset = (int) $numberingOffset;
        $this->value = $trace;
        $this->keepArgs = $keepArgs;
        $this->sliceOffset = $sliceOffset;
        $this->sliceLength = $sliceLength;
        $this->numberingOffset = $numberingOffset;
    }
}
