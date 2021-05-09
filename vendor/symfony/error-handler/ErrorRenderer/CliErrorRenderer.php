<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\ErrorHandler\ErrorRenderer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

// Help opcache.preload discover always-needed symbols
class_exists(CliDumper::class);

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CliErrorRenderer implements ErrorRendererInterface
{
    /**
     * {@inheritdoc}
     * @return \Symfony\Component\ErrorHandler\Exception\FlattenException
     */
    public function render(\Throwable $exception)
    {
        $cloner = new VarCloner();
        $dumper = new Anonymous__7bd99e4236808f08521345d76c9b0744__0();

        return FlattenException::createFromThrowable($exception)
            ->setAsString($dumper->dump($cloner->cloneVar($exception), true));
    }
}
class Anonymous__7bd99e4236808f08521345d76c9b0744__0 extends CliDumper
{
    protected function supportsColors(): bool
    {
        $outputStream = $this->outputStream;
        $this->outputStream = fopen('php://stdout', 'w');

        try {
            return parent::supportsColors();
        } finally {
            $this->outputStream = $outputStream;
        }
    }
}
