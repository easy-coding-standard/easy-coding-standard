<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\VarDumper\Test;

use ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\VarCloner;
use ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\CliDumper;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait VarDumperTestTrait
{
    /**
     * @internal
     */
    private $varDumperConfig = ['casters' => [], 'flags' => null];
    /**
     * @param mixed[] $casters
     * @param int|null $flags
     */
    protected function setUpVarDumper($casters, $flags = null) : void
    {
        $this->varDumperConfig['casters'] = $casters;
        $this->varDumperConfig['flags'] = $flags;
    }
    /**
     * @after
     */
    protected function tearDownVarDumper() : void
    {
        $this->varDumperConfig['casters'] = [];
        $this->varDumperConfig['flags'] = null;
    }
    /**
     * @param int $filter
     * @param string $message
     */
    public function assertDumpEquals($expected, $data, $filter = 0, $message = '')
    {
        $this->assertSame($this->prepareExpectation($expected, $filter), $this->getDump($data, null, $filter), $message);
    }
    /**
     * @param int $filter
     * @param string $message
     */
    public function assertDumpMatchesFormat($expected, $data, $filter = 0, $message = '')
    {
        $this->assertStringMatchesFormat($this->prepareExpectation($expected, $filter), $this->getDump($data, null, $filter), $message);
    }
    /**
     * @param int $filter
     */
    protected function getDump($data, $key = null, $filter = 0) : ?string
    {
        if (null === ($flags = $this->varDumperConfig['flags'])) {
            $flags = \getenv('DUMP_LIGHT_ARRAY') ? \ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\CliDumper::DUMP_LIGHT_ARRAY : 0;
            $flags |= \getenv('DUMP_STRING_LENGTH') ? \ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\CliDumper::DUMP_STRING_LENGTH : 0;
            $flags |= \getenv('DUMP_COMMA_SEPARATOR') ? \ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\CliDumper::DUMP_COMMA_SEPARATOR : 0;
        }
        $cloner = new \ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\VarCloner();
        $cloner->addCasters($this->varDumperConfig['casters']);
        $cloner->setMaxItems(-1);
        $dumper = new \ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\CliDumper(null, null, $flags);
        $dumper->setColors(\false);
        $data = $cloner->cloneVar($data, $filter)->withRefHandles(\false);
        if (null !== $key && null === ($data = $data->seek($key))) {
            return null;
        }
        return \rtrim($dumper->dump($data, \true));
    }
    private function prepareExpectation($expected, int $filter) : string
    {
        if (!\is_string($expected)) {
            $expected = $this->getDump($expected, null, $filter);
        }
        return \rtrim($expected);
    }
}
