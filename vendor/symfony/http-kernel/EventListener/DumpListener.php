<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpKernel\EventListener;

use ECSPrefix202306\Symfony\Component\Console\ConsoleEvents;
use ECSPrefix202306\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ECSPrefix202306\Symfony\Component\VarDumper\Cloner\ClonerInterface;
use ECSPrefix202306\Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use ECSPrefix202306\Symfony\Component\VarDumper\Server\Connection;
use ECSPrefix202306\Symfony\Component\VarDumper\VarDumper;
/**
 * Configures dump() handler.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DumpListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\VarDumper\Cloner\ClonerInterface
     */
    private $cloner;
    /**
     * @var \Symfony\Component\VarDumper\Dumper\DataDumperInterface
     */
    private $dumper;
    /**
     * @var \Symfony\Component\VarDumper\Server\Connection|null
     */
    private $connection;
    public function __construct(ClonerInterface $cloner, DataDumperInterface $dumper, Connection $connection = null)
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
        VarDumper::setHandler(static function ($var) use($cloner, $dumper, $connection) {
            $data = $cloner->cloneVar($var);
            if (!$connection || !$connection->write($data)) {
                $dumper->dump($data);
            }
        });
    }
    public static function getSubscribedEvents() : array
    {
        if (!\class_exists(ConsoleEvents::class)) {
            return [];
        }
        // Register early to have a working dump() as early as possible
        return [ConsoleEvents::COMMAND => ['configure', 1024]];
    }
}
