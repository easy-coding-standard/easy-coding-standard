<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Console\SignalRegistry;

final class SignalRegistry
{
    private $signalHandlers = [];
    public function __construct()
    {
        if (\function_exists('pcntl_async_signals')) {
            \pcntl_async_signals(\true);
        }
    }
    /**
     * @return void
     * @param int $signal
     */
    public function register($signal, callable $signalHandler)
    {
        if (!isset($this->signalHandlers[$signal])) {
            $previousCallback = \pcntl_signal_get_handler($signal);
            if (\is_callable($previousCallback)) {
                $this->signalHandlers[$signal][] = $previousCallback;
            }
        }
        $this->signalHandlers[$signal][] = $signalHandler;
        \pcntl_signal($signal, [$this, 'handle']);
    }
    /**
     * @return bool
     */
    public static function isSupported()
    {
        if (!\function_exists('pcntl_signal')) {
            return \false;
        }
        if (\in_array('pcntl_signal', \explode(',', \ini_get('disable_functions')))) {
            return \false;
        }
        return \true;
    }
    /**
     * @internal
     * @return void
     * @param int $signal
     */
    public function handle($signal)
    {
        $count = \count($this->signalHandlers[$signal]);
        foreach ($this->signalHandlers[$signal] as $i => $signalHandler) {
            $hasNext = $i !== $count - 1;
            $signalHandler($signal, $hasNext);
        }
    }
}
