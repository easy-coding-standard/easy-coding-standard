<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210514\Symfony\Component\ErrorHandler\ErrorRenderer;

use ECSPrefix20210514\Symfony\Component\ErrorHandler\Exception\FlattenException;
use ECSPrefix20210514\Symfony\Component\VarDumper\Cloner\VarCloner;
use ECSPrefix20210514\Symfony\Component\VarDumper\Dumper\CliDumper;
// Help opcache.preload discover always-needed symbols
\class_exists(\ECSPrefix20210514\Symfony\Component\VarDumper\Dumper\CliDumper::class);
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CliErrorRenderer implements \ECSPrefix20210514\Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface
{
    /**
     * {@inheritdoc}
     * @return \Symfony\Component\ErrorHandler\Exception\FlattenException
     */
    public function render(\Throwable $exception)
    {
        $cloner = new \ECSPrefix20210514\Symfony\Component\VarDumper\Cloner\VarCloner();
        $dumper = new \ECSPrefix20210514\Symfony\Component\ErrorHandler\ErrorRenderer\Anonymous__7bd99e4236808f08521345d76c9b0744__0();
        return \ECSPrefix20210514\Symfony\Component\ErrorHandler\Exception\FlattenException::createFromThrowable($exception)->setAsString($dumper->dump($cloner->cloneVar($exception), \true));
    }
}
class Anonymous__7bd99e4236808f08521345d76c9b0744__0 extends \ECSPrefix20210514\Symfony\Component\VarDumper\Dumper\CliDumper
{
    protected function supportsColors() : bool
    {
        $outputStream = $this->outputStream;
        $this->outputStream = \fopen('php://stdout', 'w');
        try {
            return parent::supportsColors();
        } finally {
            $this->outputStream = $outputStream;
        }
    }
}
