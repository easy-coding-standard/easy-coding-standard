<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpKernel\DataCollector;

use ECSPrefix202306\Symfony\Component\VarDumper\Caster\CutStub;
use ECSPrefix202306\Symfony\Component\VarDumper\Caster\ReflectionCaster;
use ECSPrefix202306\Symfony\Component\VarDumper\Cloner\ClonerInterface;
use ECSPrefix202306\Symfony\Component\VarDumper\Cloner\Data;
use ECSPrefix202306\Symfony\Component\VarDumper\Cloner\Stub;
use ECSPrefix202306\Symfony\Component\VarDumper\Cloner\VarCloner;
/**
 * DataCollector.
 *
 * Children of this class must store the collected data in the data property.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bernhard Schussek <bschussek@symfony.com>
 */
abstract class DataCollector implements DataCollectorInterface
{
    /**
     * @var array|Data
     */
    protected $data = [];
    /**
     * @var \Symfony\Component\VarDumper\Cloner\ClonerInterface
     */
    private $cloner;
    /**
     * Converts the variable into a serializable Data instance.
     *
     * This array can be displayed in the template using
     * the VarDumper component.
     * @param mixed $var
     */
    protected function cloneVar($var) : Data
    {
        if ($var instanceof Data) {
            return $var;
        }
        if (!isset($this->cloner)) {
            $this->cloner = new VarCloner();
            $this->cloner->setMaxItems(-1);
            $this->cloner->addCasters($this->getCasters());
        }
        return $this->cloner->cloneVar($var);
    }
    /**
     * @return callable[] The casters to add to the cloner
     */
    protected function getCasters()
    {
        $casters = ['*' => function ($v, array $a, Stub $s, $isNested) {
            if (!$v instanceof Stub) {
                foreach ($a as $k => $v) {
                    if (\is_object($v) && !$v instanceof \DateTimeInterface && !$v instanceof Stub) {
                        $a[$k] = new CutStub($v);
                    }
                }
            }
            return $a;
        }] + ReflectionCaster::UNSET_CLOSURE_FILE_INFO;
        return $casters;
    }
    public function __sleep() : array
    {
        return ['data'];
    }
    public function __wakeup()
    {
    }
    /**
     * @internal to prevent implementing \Serializable
     */
    protected final function serialize()
    {
    }
    /**
     * @internal to prevent implementing \Serializable
     */
    protected final function unserialize(string $data)
    {
    }
}
