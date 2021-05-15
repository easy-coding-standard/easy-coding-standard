<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210515\Symfony\Component\VarDumper\Caster;

use ECSPrefix20210515\Symfony\Component\ErrorHandler\Exception\SilencedErrorContext;
use ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub;
use ECSPrefix20210515\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * Casts common Exception classes to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 */
class ExceptionCaster
{
    public static $srcContext = 1;
    public static $traceArgs = \true;
    public static $errorTypes = [\E_DEPRECATED => 'E_DEPRECATED', \E_USER_DEPRECATED => 'E_USER_DEPRECATED', \E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR', \E_ERROR => 'E_ERROR', \E_WARNING => 'E_WARNING', \E_PARSE => 'E_PARSE', \E_NOTICE => 'E_NOTICE', \E_CORE_ERROR => 'E_CORE_ERROR', \E_CORE_WARNING => 'E_CORE_WARNING', \E_COMPILE_ERROR => 'E_COMPILE_ERROR', \E_COMPILE_WARNING => 'E_COMPILE_WARNING', \E_USER_ERROR => 'E_USER_ERROR', \E_USER_WARNING => 'E_USER_WARNING', \E_USER_NOTICE => 'E_USER_NOTICE', \E_STRICT => 'E_STRICT'];
    private static $framesCache = [];
    /**
     * @param bool $isNested
     * @param int $filter
     */
    public static function castError(\Error $e, array $a, \ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested, $filter = 0)
    {
        $isNested = (bool) $isNested;
        $filter = (int) $filter;
        return self::filterExceptionArray($stub->class, $a, "\0Error\0", $filter);
    }
    /**
     * @param bool $isNested
     * @param int $filter
     */
    public static function castException(\Exception $e, array $a, \ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested, $filter = 0)
    {
        $isNested = (bool) $isNested;
        $filter = (int) $filter;
        return self::filterExceptionArray($stub->class, $a, "\0Exception\0", $filter);
    }
    /**
     * @param bool $isNested
     */
    public static function castErrorException(\ErrorException $e, array $a, \ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        if (isset($a[$s = \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'severity'], self::$errorTypes[$a[$s]])) {
            $a[$s] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ConstStub(self::$errorTypes[$a[$s]], $a[$s]);
        }
        return $a;
    }
    /**
     * @param bool $isNested
     */
    public static function castThrowingCasterException(\ECSPrefix20210515\Symfony\Component\VarDumper\Exception\ThrowingCasterException $e, array $a, \ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        $trace = \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'trace';
        $prefix = \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED;
        $xPrefix = "\0Exception\0";
        if (isset($a[$xPrefix . 'previous'], $a[$trace]) && $a[$xPrefix . 'previous'] instanceof \Exception) {
            $b = (array) $a[$xPrefix . 'previous'];
            $class = \get_debug_type($a[$xPrefix . 'previous']);
            self::traceUnshift($b[$xPrefix . 'trace'], $class, $b[$prefix . 'file'], $b[$prefix . 'line']);
            $a[$trace] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\TraceStub($b[$xPrefix . 'trace'], \false, 0, -\count($a[$trace]->value));
        }
        unset($a[$xPrefix . 'previous'], $a[$prefix . 'code'], $a[$prefix . 'file'], $a[$prefix . 'line']);
        return $a;
    }
    /**
     * @param bool $isNested
     */
    public static function castSilencedErrorContext(\ECSPrefix20210515\Symfony\Component\ErrorHandler\Exception\SilencedErrorContext $e, array $a, \ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        $sPrefix = "\0" . \ECSPrefix20210515\Symfony\Component\ErrorHandler\Exception\SilencedErrorContext::class . "\0";
        if (!isset($a[$s = $sPrefix . 'severity'])) {
            return $a;
        }
        if (isset(self::$errorTypes[$a[$s]])) {
            $a[$s] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ConstStub(self::$errorTypes[$a[$s]], $a[$s]);
        }
        $trace = [['file' => $a[$sPrefix . 'file'], 'line' => $a[$sPrefix . 'line']]];
        if (isset($a[$sPrefix . 'trace'])) {
            $trace = \array_merge($trace, $a[$sPrefix . 'trace']);
        }
        unset($a[$sPrefix . 'file'], $a[$sPrefix . 'line'], $a[$sPrefix . 'trace']);
        $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'trace'] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\TraceStub($trace, self::$traceArgs);
        return $a;
    }
    /**
     * @param bool $isNested
     */
    public static function castTraceStub(\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\TraceStub $trace, array $a, \ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        if (!$isNested) {
            return $a;
        }
        $stub->class = '';
        $stub->handle = 0;
        $frames = $trace->value;
        $prefix = \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $a = [];
        $j = \count($frames);
        if (0 > ($i = $trace->sliceOffset)) {
            $i = \max(0, $j + $i);
        }
        if (!isset($trace->value[$i])) {
            return [];
        }
        $lastCall = isset($frames[$i]['function']) ? (isset($frames[$i]['class']) ? $frames[0]['class'] . $frames[$i]['type'] : '') . $frames[$i]['function'] . '()' : '';
        $frames[] = ['function' => ''];
        $collapse = \false;
        for ($j += $trace->numberingOffset - $i++; isset($frames[$i]); ++$i, --$j) {
            $f = $frames[$i];
            $call = isset($f['function']) ? (isset($f['class']) ? $f['class'] . $f['type'] : '') . $f['function'] : '???';
            $frame = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\FrameStub(['object' => isset($f['object']) ? $f['object'] : null, 'class' => isset($f['class']) ? $f['class'] : null, 'type' => isset($f['type']) ? $f['type'] : null, 'function' => isset($f['function']) ? $f['function'] : null] + $frames[$i - 1], \false, \true);
            $f = self::castFrameStub($frame, [], $frame, \true);
            if (isset($f[$prefix . 'src'])) {
                foreach ($f[$prefix . 'src']->value as $label => $frame) {
                    if (0 === \strpos($label, "\0~collapse=0")) {
                        if ($collapse) {
                            $label = \substr_replace($label, '1', 11, 1);
                        } else {
                            $collapse = \true;
                        }
                    }
                    $label = \substr_replace($label, "title=Stack level {$j}.&", 2, 0);
                }
                $f = $frames[$i - 1];
                if ($trace->keepArgs && !empty($f['args']) && $frame instanceof \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\EnumStub) {
                    $frame->value['arguments'] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ArgsStub($f['args'], isset($f['function']) ? $f['function'] : null, isset($f['class']) ? $f['class'] : null);
                }
            } elseif ('???' !== $lastCall) {
                $label = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ClassStub($lastCall);
                if (isset($label->attr['ellipsis'])) {
                    $label->attr['ellipsis'] += 2;
                    $label = \substr_replace($prefix, "ellipsis-type=class&ellipsis={$label->attr['ellipsis']}&ellipsis-tail=1&title=Stack level {$j}.", 2, 0) . $label->value . '()';
                } else {
                    $label = \substr_replace($prefix, "title=Stack level {$j}.", 2, 0) . $label->value . '()';
                }
            } else {
                $label = \substr_replace($prefix, "title=Stack level {$j}.", 2, 0) . $lastCall;
            }
            $a[\substr_replace($label, \sprintf('separator=%s&', $frame instanceof \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\EnumStub ? ' ' : ':'), 2, 0)] = $frame;
            $lastCall = $call;
        }
        if (null !== $trace->sliceLength) {
            $a = \array_slice($a, 0, $trace->sliceLength, \true);
        }
        return $a;
    }
    /**
     * @param bool $isNested
     */
    public static function castFrameStub(\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\FrameStub $frame, array $a, \ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        if (!$isNested) {
            return $a;
        }
        $f = $frame->value;
        $prefix = \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        if (isset($f['file'], $f['line'])) {
            $cacheKey = $f;
            unset($cacheKey['object'], $cacheKey['args']);
            $cacheKey[] = self::$srcContext;
            $cacheKey = \implode('-', $cacheKey);
            if (isset(self::$framesCache[$cacheKey])) {
                $a[$prefix . 'src'] = self::$framesCache[$cacheKey];
            } else {
                if (\preg_match('/\\((\\d+)\\)(?:\\([\\da-f]{32}\\))? : (?:eval\\(\\)\'d code|runtime-created function)$/', $f['file'], $match)) {
                    $f['file'] = \substr($f['file'], 0, -\strlen($match[0]));
                    $f['line'] = (int) $match[1];
                }
                $src = $f['line'];
                $srcKey = $f['file'];
                $ellipsis = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\LinkStub($srcKey, 0);
                $srcAttr = 'collapse=' . (int) $ellipsis->inVendor;
                $ellipsisTail = isset($ellipsis->attr['ellipsis-tail']) ? $ellipsis->attr['ellipsis-tail'] : 0;
                $ellipsis = isset($ellipsis->attr['ellipsis']) ? $ellipsis->attr['ellipsis'] : 0;
                if (\is_file($f['file']) && 0 <= self::$srcContext) {
                    if (!empty($f['class']) && (\is_subclass_of($f['class'], 'ECSPrefix20210515\\Twig\\Template') || \is_subclass_of($f['class'], 'ECSPrefix20210515\\Twig_Template')) && \method_exists($f['class'], 'getDebugInfo')) {
                        $template = isset($f['object']) ? $f['object'] : \unserialize(\sprintf('O:%d:"%s":0:{}', \strlen($f['class']), $f['class']));
                        $ellipsis = 0;
                        $templateSrc = \method_exists($template, 'getSourceContext') ? $template->getSourceContext()->getCode() : (\method_exists($template, 'getSource') ? $template->getSource() : '');
                        $templateInfo = $template->getDebugInfo();
                        if (isset($templateInfo[$f['line']])) {
                            if (!\method_exists($template, 'getSourceContext') || !\is_file($templatePath = $template->getSourceContext()->getPath())) {
                                $templatePath = null;
                            }
                            if ($templateSrc) {
                                $src = self::extractSource($templateSrc, $templateInfo[$f['line']], self::$srcContext, 'twig', $templatePath, $f);
                                $srcKey = ($templatePath ?: $template->getTemplateName()) . ':' . $templateInfo[$f['line']];
                            }
                        }
                    }
                    if ($srcKey == $f['file']) {
                        $src = self::extractSource(\file_get_contents($f['file']), $f['line'], self::$srcContext, 'php', $f['file'], $f);
                        $srcKey .= ':' . $f['line'];
                        if ($ellipsis) {
                            $ellipsis += 1 + \strlen($f['line']);
                        }
                    }
                    $srcAttr .= \sprintf('&separator= &file=%s&line=%d', \rawurlencode($f['file']), $f['line']);
                } else {
                    $srcAttr .= '&separator=:';
                }
                $srcAttr .= $ellipsis ? '&ellipsis-type=path&ellipsis=' . $ellipsis . '&ellipsis-tail=' . $ellipsisTail : '';
                self::$framesCache[$cacheKey] = $a[$prefix . 'src'] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\EnumStub(["\0~{$srcAttr}\0{$srcKey}" => $src]);
            }
        }
        unset($a[$prefix . 'args'], $a[$prefix . 'line'], $a[$prefix . 'file']);
        if ($frame->inTraceStub) {
            unset($a[$prefix . 'class'], $a[$prefix . 'type'], $a[$prefix . 'function']);
        }
        foreach ($a as $k => $v) {
            if (!$v) {
                unset($a[$k]);
            }
        }
        if ($frame->keepArgs && !empty($f['args'])) {
            $a[$prefix . 'arguments'] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ArgsStub($f['args'], $f['function'], $f['class']);
        }
        return $a;
    }
    /**
     * @param string $xClass
     * @param string $xPrefix
     * @param int $filter
     * @return mixed[]
     */
    private static function filterExceptionArray($xClass, array $a, $xPrefix, $filter)
    {
        $xClass = (string) $xClass;
        $xPrefix = (string) $xPrefix;
        $filter = (int) $filter;
        if (isset($a[$xPrefix . 'trace'])) {
            $trace = $a[$xPrefix . 'trace'];
            unset($a[$xPrefix . 'trace']);
            // Ensures the trace is always last
        } else {
            $trace = [];
        }
        if (!($filter & \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::EXCLUDE_VERBOSE) && $trace) {
            if (isset($a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'file'], $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'line'])) {
                self::traceUnshift($trace, $xClass, $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'file'], $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'line']);
            }
            $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'trace'] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\TraceStub($trace, self::$traceArgs);
        }
        if (empty($a[$xPrefix . 'previous'])) {
            unset($a[$xPrefix . 'previous']);
        }
        unset($a[$xPrefix . 'string'], $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_DYNAMIC . 'xdebug_message'], $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_DYNAMIC . '__destructorException']);
        if (isset($a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'message']) && \false !== \strpos($a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'message'], "@anonymous\0")) {
            $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'message'] = \preg_replace_callback('/[a-zA-Z_\\x7f-\\xff][\\\\a-zA-Z0-9_\\x7f-\\xff]*+@anonymous\\x00.*?\\.php(?:0x?|:[0-9]++\\$)[0-9a-fA-F]++/', function ($m) {
                return \class_exists($m[0], \false) ? ((\get_parent_class($m[0]) ?: \key(\class_implements($m[0]))) ?: 'class') . '@anonymous' : $m[0];
            }, $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'message']);
        }
        if (isset($a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'file'], $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'line'])) {
            $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'file'] = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\LinkStub($a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'file'], $a[\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'line']);
        }
        return $a;
    }
    /**
     * @param string|null $class
     * @return void
     * @param string $file
     * @param int $line
     */
    private static function traceUnshift(array &$trace, $class, $file, $line)
    {
        $file = (string) $file;
        $line = (int) $line;
        if (isset($trace[0]['file'], $trace[0]['line']) && $trace[0]['file'] === $file && $trace[0]['line'] === $line) {
            return;
        }
        \array_unshift($trace, ['function' => $class ? 'new ' . $class : null, 'file' => $file, 'line' => $line]);
    }
    /**
     * @param string|null $file
     * @param string $srcLines
     * @param int $line
     * @param int $srcContext
     * @param string $lang
     * @return \Symfony\Component\VarDumper\Caster\EnumStub
     */
    private static function extractSource($srcLines, $line, $srcContext, $lang, $file, array $frame)
    {
        $srcLines = (string) $srcLines;
        $line = (int) $line;
        $srcContext = (int) $srcContext;
        $lang = (string) $lang;
        $srcLines = \explode("\n", $srcLines);
        $src = [];
        for ($i = $line - 1 - $srcContext; $i <= $line - 1 + $srcContext; ++$i) {
            $src[] = (isset($srcLines[$i]) ? $srcLines[$i] : '') . "\n";
        }
        if (isset($frame['function']) ? $frame['function'] : \false) {
            $stub = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\CutStub(new \stdClass());
            $stub->class = (isset($frame['class']) ? $frame['class'] . $frame['type'] : '') . $frame['function'];
            $stub->type = \ECSPrefix20210515\Symfony\Component\VarDumper\Cloner\Stub::TYPE_OBJECT;
            $stub->attr['cut_hash'] = \true;
            $stub->attr['file'] = $frame['file'];
            $stub->attr['line'] = $frame['line'];
            try {
                $caller = isset($frame['class']) ? new \ReflectionMethod($frame['class'], $frame['function']) : new \ReflectionFunction($frame['function']);
                $stub->class .= \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ReflectionCaster::getSignature(\ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ReflectionCaster::castFunctionAbstract($caller, [], $stub, \true, \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\Caster::EXCLUDE_VERBOSE));
                if ($f = $caller->getFileName()) {
                    $stub->attr['file'] = $f;
                    $stub->attr['line'] = $caller->getStartLine();
                }
            } catch (\ReflectionException $e) {
                // ignore fake class/function
            }
            $srcLines = ["\0~separator=\0" => $stub];
        } else {
            $stub = null;
            $srcLines = [];
        }
        $ltrim = 0;
        do {
            $pad = null;
            for ($i = $srcContext << 1; $i >= 0; --$i) {
                if (isset($src[$i][$ltrim]) && "\r" !== ($c = $src[$i][$ltrim]) && "\n" !== $c) {
                    if (null === $pad) {
                        $pad = $c;
                    }
                    if (' ' !== $c && "\t" !== $c || $pad !== $c) {
                        break;
                    }
                }
            }
            ++$ltrim;
        } while (0 > $i && null !== $pad);
        --$ltrim;
        foreach ($src as $i => $c) {
            if ($ltrim) {
                $c = isset($c[$ltrim]) && "\r" !== $c[$ltrim] ? \substr($c, $ltrim) : \ltrim($c, " \t");
            }
            $c = \substr($c, 0, -1);
            if ($i !== $srcContext) {
                $c = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ConstStub('default', $c);
            } else {
                $c = new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\ConstStub($c, $stub ? 'in ' . $stub->class : '');
                if (null !== $file) {
                    $c->attr['file'] = $file;
                    $c->attr['line'] = $line;
                }
            }
            $c->attr['lang'] = $lang;
            $srcLines[\sprintf("\0~separator=› &%d\0", $i + $line - $srcContext)] = $c;
        }
        return new \ECSPrefix20210515\Symfony\Component\VarDumper\Caster\EnumStub($srcLines);
    }
}
