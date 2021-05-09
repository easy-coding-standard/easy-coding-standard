<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210509\Symfony\Component\ErrorHandler\Exception;

use ECSPrefix20210509\Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use ECSPrefix20210509\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20210509\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
/**
 * FlattenException wraps a PHP Error or Exception to be able to serialize it.
 *
 * Basically, this class removes all objects from the trace.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FlattenException
{
    /** @var string */
    private $message;
    /** @var int|string */
    private $code;
    /** @var self|null */
    private $previous;
    /** @var array */
    private $trace;
    /** @var string */
    private $traceAsString;
    /** @var string */
    private $class;
    /** @var int */
    private $statusCode;
    /** @var string */
    private $statusText;
    /** @var array */
    private $headers;
    /** @var string */
    private $file;
    /** @var int */
    private $line;
    /** @var string|null */
    private $asString;
    /**
     * @return static
     */
    public static function create(\Exception $exception, $statusCode = null, array $headers = [])
    {
        return static::createFromThrowable($exception, $statusCode, $headers);
    }
    /**
     * @return static
     * @param int $statusCode
     */
    public static function createFromThrowable(\Throwable $exception, $statusCode = null, array $headers = [])
    {
        $e = new static();
        $e->setMessage($exception->getMessage());
        $e->setCode($exception->getCode());
        if ($exception instanceof \ECSPrefix20210509\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $headers = \array_merge($headers, $exception->getHeaders());
        } elseif ($exception instanceof \ECSPrefix20210509\Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface) {
            $statusCode = 400;
        }
        if (null === $statusCode) {
            $statusCode = 500;
        }
        if (\class_exists(\ECSPrefix20210509\Symfony\Component\HttpFoundation\Response::class) && isset(\ECSPrefix20210509\Symfony\Component\HttpFoundation\Response::$statusTexts[$statusCode])) {
            $statusText = \ECSPrefix20210509\Symfony\Component\HttpFoundation\Response::$statusTexts[$statusCode];
        } else {
            $statusText = 'Whoops, looks like something went wrong.';
        }
        $e->setStatusText($statusText);
        $e->setStatusCode($statusCode);
        $e->setHeaders($headers);
        $e->setTraceFromThrowable($exception);
        $e->setClass(\get_debug_type($exception));
        $e->setFile($exception->getFile());
        $e->setLine($exception->getLine());
        $previous = $exception->getPrevious();
        if ($previous instanceof \Throwable) {
            $e->setPrevious(static::createFromThrowable($previous));
        }
        return $e;
    }
    /**
     * @return mixed[]
     */
    public function toArray()
    {
        $exceptions = [];
        foreach (\array_merge([$this], $this->getAllPrevious()) as $exception) {
            $exceptions[] = ['message' => $exception->getMessage(), 'class' => $exception->getClass(), 'trace' => $exception->getTrace()];
        }
        return $exceptions;
    }
    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    /**
     * @param int $code
     *
     * @return $this
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }
    /**
     * @return mixed[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    /**
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }
    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = \false !== \strpos($class, "@anonymous\0") ? ((\get_parent_class($class) ?: \key(\class_implements($class))) ?: 'class') . '@anonymous' : $class;
        return $this;
    }
    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }
    /**
     * @param int $line
     *
     * @return $this
     */
    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }
    /**
     * @return string
     */
    public function getStatusText()
    {
        return $this->statusText;
    }
    /**
     * @return $this
     * @param string $statusText
     */
    public function setStatusText($statusText)
    {
        $statusText = (string) $statusText;
        $this->statusText = $statusText;
        return $this;
    }
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        if (\false !== \strpos($message, "@anonymous\0")) {
            $message = \preg_replace_callback('/[a-zA-Z_\\x7f-\\xff][\\\\a-zA-Z0-9_\\x7f-\\xff]*+@anonymous\\x00.*?\\.php(?:0x?|:[0-9]++\\$)[0-9a-fA-F]++/', function ($m) {
                return \class_exists($m[0], \false) ? ((\get_parent_class($m[0]) ?: \key(\class_implements($m[0]))) ?: 'class') . '@anonymous' : $m[0];
            }, $message);
        }
        $this->message = $message;
        return $this;
    }
    /**
     * @return int|string int most of the time (might be a string with PDOException)
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * @param int|string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    /**
     * @return $this|null
     */
    public function getPrevious()
    {
        return $this->previous;
    }
    /**
     * @return $this
     * @param $this $previous
     */
    public function setPrevious($previous)
    {
        $this->previous = $previous;
        return $this;
    }
    /**
     * @return mixed[]
     */
    public function getAllPrevious()
    {
        $exceptions = [];
        $e = $this;
        while ($e = $e->getPrevious()) {
            $exceptions[] = $e;
        }
        return $exceptions;
    }
    /**
     * @return mixed[]
     */
    public function getTrace()
    {
        return $this->trace;
    }
    /**
     * @return $this
     */
    public function setTraceFromThrowable(\Throwable $throwable)
    {
        $this->traceAsString = $throwable->getTraceAsString();
        return $this->setTrace($throwable->getTrace(), $throwable->getFile(), $throwable->getLine());
    }
    /**
     * @param array       $trace
     * @param string|null $file
     * @param int|null    $line
     *
     * @return $this
     */
    public function setTrace($trace, $file, $line)
    {
        $this->trace = [];
        $this->trace[] = ['namespace' => '', 'short_class' => '', 'class' => '', 'type' => '', 'function' => '', 'file' => $file, 'line' => $line, 'args' => []];
        foreach ($trace as $entry) {
            $class = '';
            $namespace = '';
            if (isset($entry['class'])) {
                $parts = \explode('\\', $entry['class']);
                $class = \array_pop($parts);
                $namespace = \implode('\\', $parts);
            }
            $this->trace[] = ['namespace' => $namespace, 'short_class' => $class, 'class' => isset($entry['class']) ? $entry['class'] : '', 'type' => isset($entry['type']) ? $entry['type'] : '', 'function' => isset($entry['function']) ? $entry['function'] : null, 'file' => isset($entry['file']) ? $entry['file'] : null, 'line' => isset($entry['line']) ? $entry['line'] : null, 'args' => isset($entry['args']) ? $this->flattenArgs($entry['args']) : []];
        }
        return $this;
    }
    /**
     * @param int $level
     * @param int $count
     * @return mixed[]
     */
    private function flattenArgs(array $args, $level = 0, &$count = 0)
    {
        $level = (int) $level;
        $count = (int) $count;
        $result = [];
        foreach ($args as $key => $value) {
            if (++$count > 10000.0) {
                return ['array', '*SKIPPED over 10000 entries*'];
            }
            if ($value instanceof \__PHP_Incomplete_Class) {
                // is_object() returns false on PHP<=7.1
                $result[$key] = ['incomplete-object', $this->getClassNameFromIncomplete($value)];
            } elseif (\is_object($value)) {
                $result[$key] = ['object', \get_class($value)];
            } elseif (\is_array($value)) {
                if ($level > 10) {
                    $result[$key] = ['array', '*DEEP NESTED ARRAY*'];
                } else {
                    $result[$key] = ['array', $this->flattenArgs($value, $level + 1, $count)];
                }
            } elseif (null === $value) {
                $result[$key] = ['null', null];
            } elseif (\is_bool($value)) {
                $result[$key] = ['boolean', $value];
            } elseif (\is_int($value)) {
                $result[$key] = ['integer', $value];
            } elseif (\is_float($value)) {
                $result[$key] = ['float', $value];
            } elseif (\is_resource($value)) {
                $result[$key] = ['resource', \get_resource_type($value)];
            } else {
                $result[$key] = ['string', (string) $value];
            }
        }
        return $result;
    }
    /**
     * @return string
     */
    private function getClassNameFromIncomplete(\__PHP_Incomplete_Class $value)
    {
        $array = new \ArrayObject($value);
        return $array['__PHP_Incomplete_Class_Name'];
    }
    /**
     * @return string
     */
    public function getTraceAsString()
    {
        return $this->traceAsString;
    }
    /**
     * @return $this
     * @param string|null $asString
     */
    public function setAsString($asString)
    {
        $this->asString = $asString;
        return $this;
    }
    /**
     * @return string
     */
    public function getAsString()
    {
        if (null !== $this->asString) {
            return $this->asString;
        }
        $message = '';
        $next = \false;
        foreach (\array_reverse(\array_merge([$this], $this->getAllPrevious())) as $exception) {
            if ($next) {
                $message .= 'Next ';
            } else {
                $next = \true;
            }
            $message .= $exception->getClass();
            if ('' != $exception->getMessage()) {
                $message .= ': ' . $exception->getMessage();
            }
            $message .= ' in ' . $exception->getFile() . ':' . $exception->getLine() . "\nStack trace:\n" . $exception->getTraceAsString() . "\n\n";
        }
        return \rtrim($message);
    }
}
