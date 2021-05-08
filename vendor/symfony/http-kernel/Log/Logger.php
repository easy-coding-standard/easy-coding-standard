<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\HttpKernel\Log;

use ECSPrefix20210508\Psr\Log\AbstractLogger;
use ECSPrefix20210508\Psr\Log\InvalidArgumentException;
use ECSPrefix20210508\Psr\Log\LogLevel;
/**
 * Minimalist PSR-3 logger designed to write in stderr or any other stream.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class Logger extends \ECSPrefix20210508\Psr\Log\AbstractLogger
{
    const LEVELS = [\ECSPrefix20210508\Psr\Log\LogLevel::DEBUG => 0, \ECSPrefix20210508\Psr\Log\LogLevel::INFO => 1, \ECSPrefix20210508\Psr\Log\LogLevel::NOTICE => 2, \ECSPrefix20210508\Psr\Log\LogLevel::WARNING => 3, \ECSPrefix20210508\Psr\Log\LogLevel::ERROR => 4, \ECSPrefix20210508\Psr\Log\LogLevel::CRITICAL => 5, \ECSPrefix20210508\Psr\Log\LogLevel::ALERT => 6, \ECSPrefix20210508\Psr\Log\LogLevel::EMERGENCY => 7];
    private $minLevelIndex;
    private $formatter;
    private $handle;
    /**
     * @param string $minLevel
     */
    public function __construct($minLevel = null, $output = null, callable $formatter = null)
    {
        if (null === $minLevel) {
            $minLevel = null === $output || 'php://stdout' === $output || 'php://stderr' === $output ? \ECSPrefix20210508\Psr\Log\LogLevel::ERROR : \ECSPrefix20210508\Psr\Log\LogLevel::WARNING;
            if (isset($_ENV['SHELL_VERBOSITY']) || isset($_SERVER['SHELL_VERBOSITY'])) {
                switch ((int) (isset($_ENV['SHELL_VERBOSITY']) ? $_ENV['SHELL_VERBOSITY'] : $_SERVER['SHELL_VERBOSITY'])) {
                    case -1:
                        $minLevel = \ECSPrefix20210508\Psr\Log\LogLevel::ERROR;
                        break;
                    case 1:
                        $minLevel = \ECSPrefix20210508\Psr\Log\LogLevel::NOTICE;
                        break;
                    case 2:
                        $minLevel = \ECSPrefix20210508\Psr\Log\LogLevel::INFO;
                        break;
                    case 3:
                        $minLevel = \ECSPrefix20210508\Psr\Log\LogLevel::DEBUG;
                        break;
                }
            }
        }
        if (!isset(self::LEVELS[$minLevel])) {
            throw new \ECSPrefix20210508\Psr\Log\InvalidArgumentException(\sprintf('The log level "%s" does not exist.', $minLevel));
        }
        $this->minLevelIndex = self::LEVELS[$minLevel];
        $this->formatter = $formatter ?: [$this, 'format'];
        if ($output && \false === ($this->handle = \is_resource($output) ? $output : @\fopen($output, 'a'))) {
            throw new \ECSPrefix20210508\Psr\Log\InvalidArgumentException(\sprintf('Unable to open "%s".', $output));
        }
    }
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if (!isset(self::LEVELS[$level])) {
            throw new \ECSPrefix20210508\Psr\Log\InvalidArgumentException(\sprintf('The log level "%s" does not exist.', $level));
        }
        if (self::LEVELS[$level] < $this->minLevelIndex) {
            return;
        }
        $formatter = $this->formatter;
        if ($this->handle) {
            @\fwrite($this->handle, $formatter($level, $message, $context));
        } else {
            \error_log($formatter($level, $message, $context, \false));
        }
    }
    /**
     * @param string $level
     * @param string $message
     * @param bool $prefixDate
     * @return string
     */
    private function format($level, $message, array $context, $prefixDate = \true)
    {
        if (\false !== \strpos($message, '{')) {
            $replacements = [];
            foreach ($context as $key => $val) {
                if (null === $val || \is_scalar($val) || \is_object($val) && \method_exists($val, '__toString')) {
                    $replacements["{{$key}}"] = $val;
                } elseif ($val instanceof \DateTimeInterface) {
                    $replacements["{{$key}}"] = $val->format(\DateTime::RFC3339);
                } elseif (\is_object($val)) {
                    $replacements["{{$key}}"] = '[object ' . \get_class($val) . ']';
                } else {
                    $replacements["{{$key}}"] = '[' . \gettype($val) . ']';
                }
            }
            $message = \strtr($message, $replacements);
        }
        $log = \sprintf('[%s] %s', $level, $message) . \PHP_EOL;
        if ($prefixDate) {
            $log = \date(\DateTime::RFC3339) . ' ' . $log;
        }
        return $log;
    }
}