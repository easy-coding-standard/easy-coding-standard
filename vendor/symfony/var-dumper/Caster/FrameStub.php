<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\VarDumper\Caster;

/**
 * Represents a single backtrace frame as returned by debug_backtrace() or Exception->getTrace().
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class FrameStub extends \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\EnumStub
{
    public $keepArgs;
    public $inTraceStub;
    /**
     * @param bool $keepArgs
     * @param bool $inTraceStub
     */
    public function __construct(array $frame, $keepArgs = \true, $inTraceStub = \false)
    {
        $this->value = $frame;
        $this->keepArgs = $keepArgs;
        $this->inTraceStub = $inTraceStub;
    }
}
