<?php

declare (strict_types=1);
namespace ECSPrefix20211130;

/*
 * This file is part of Evenement.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
const ITERATIONS = 10000000;
use ECSPrefix20211130\Evenement\EventEmitter;
require __DIR__ . '/../vendor/autoload.php';
$emitter = new \ECSPrefix20211130\Evenement\EventEmitter();
$emitter->on('event', function ($a, $b, $c) {
});
$start = \microtime(\true);
for ($i = 0; $i < \ITERATIONS; $i++) {
    $emitter->emit('event', [1, 2, 3]);
}
$time = \microtime(\true) - $start;
echo 'Emitting ', \number_format(\ITERATIONS), ' events took: ', \number_format($time, 2), 's', \PHP_EOL;
