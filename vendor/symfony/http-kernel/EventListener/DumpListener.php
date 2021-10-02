<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpKernel\EventListener;

use ECSPrefix20211002\Symfony\Component\Console\ConsoleEvents;
use ECSPrefix20211002\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\ClonerInterface;
use ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use ECSPrefix20211002\Symfony\Component\VarDumper\Server\Connection;
use ECSPrefix20211002\Symfony\Component\VarDumper\VarDumper;
/**
 * Configures dump() handler.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DumpListener implements \ECSPrefix20211002\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $cloner;
    private $dumper;
    private $connection;
    public function __construct(\ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\ClonerInterface $cloner, \ECSPrefix20211002\Symfony\Component\VarDumper\Dumper\DataDumperInterface $dumper, \ECSPrefix20211002\Symfony\Component\VarDumper\Server\Connection $connection = null)
    {
        $this->cloner = $cloner;
        $this->dumper = $dumper;
        $this->connection = $connection;
    }
    public function configure()
    {
        $cloner = $this->cloner;
        $dumper = $this->dumper;
        $connection = $this->connection;
        \ECSPrefix20211002\Symfony\Component\VarDumper\VarDumper::setHandler(static function ($var) use($cloner, $dumper, $connection) {
            $data = $cloner->cloneVar($var);
            if (!$connection || !$connection->write($data)) {
                $dumper->dump($data);
            }
        });
    }
    public static function getSubscribedEvents()
    {
        if (!\class_exists(\ECSPrefix20211002\Symfony\Component\Console\ConsoleEvents::class)) {
            return [];
        }
        // Register early to have a working dump() as early as possible
        return [\ECSPrefix20211002\Symfony\Component\Console\ConsoleEvents::COMMAND => ['configure', 1024]];
    }
}
