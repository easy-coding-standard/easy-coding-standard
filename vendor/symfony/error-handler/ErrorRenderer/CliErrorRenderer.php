<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\ErrorHandler\ErrorRenderer;

use ECSPrefix20211002\Symfony\Component\ErrorHandler\Exception\FlattenException;
use ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\VarCloner;
use ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\CliDumper;
// Help opcache.preload discover always-needed symbols
\class_exists(\ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\CliDumper::class);
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CliErrorRenderer implements \ECSPrefix20211002\Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface
{
    /**
     * {@inheritdoc}
     * @param \Throwable $exception
     */
    public function render($exception) : \ECSPrefix20211002\Symfony\Component\ErrorHandler\Exception\FlattenException
    {
        $cloner = new \ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\VarCloner();
        $dumper = new class extends \ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\CliDumper
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
        };
        return \ECSPrefix20211002\Symfony\Component\ErrorHandler\Exception\FlattenException::createFromThrowable($exception)->setAsString($dumper->dump($cloner->cloneVar($exception), \true));
    }
}
