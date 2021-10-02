<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Contracts\HttpClient\Test;

use ECSPrefix20211002\Symfony\Component\Process\PhpExecutableFinder;
use ECSPrefix20211002\Symfony\Component\Process\Process;
class TestHttpServer
{
    private static $process = [];
    /**
     * @param int $port
     */
    public static function start($port = 8057)
    {
        if (isset(self::$process[$port])) {
            self::$process[$port]->stop();
        } else {
            \register_shutdown_function(static function () use($port) {
                self::$process[$port]->stop();
            });
        }
        $finder = new \ECSPrefix20211002\Symfony\Component\Process\PhpExecutableFinder();
        $process = new \ECSPrefix20211002\Symfony\Component\Process\Process(\array_merge([$finder->find(\false)], $finder->findArguments(), ['-dopcache.enable=0', '-dvariables_order=EGPCS', '-S', '127.0.0.1:' . $port]));
        $process->setWorkingDirectory(__DIR__ . '/Fixtures/web');
        $process->start();
        self::$process[$port] = $process;
        do {
            \usleep(50000);
        } while (!@\fopen('http://127.0.0.1:' . $port, 'r'));
        return $process;
    }
}
