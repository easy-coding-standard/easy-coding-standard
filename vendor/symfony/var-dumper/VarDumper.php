<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\VarDumper;

use ECSPrefix202306\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202306\Symfony\Component\HttpFoundation\RequestStack;
use ECSPrefix202306\Symfony\Component\HttpKernel\Debug\FileLinkFormatter;
use ECSPrefix202306\Symfony\Component\VarDumper\Caster\ReflectionCaster;
use ECSPrefix202306\Symfony\Component\VarDumper\Cloner\VarCloner;
use ECSPrefix202306\Symfony\Component\VarDumper\Dumper\CliDumper;
use ECSPrefix202306\Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use ECSPrefix202306\Symfony\Component\VarDumper\Dumper\ContextProvider\RequestContextProvider;
use ECSPrefix202306\Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use ECSPrefix202306\Symfony\Component\VarDumper\Dumper\ContextualizedDumper;
use ECSPrefix202306\Symfony\Component\VarDumper\Dumper\HtmlDumper;
use ECSPrefix202306\Symfony\Component\VarDumper\Dumper\ServerDumper;
// Load the global dump() function
require_once __DIR__ . '/Resources/functions/dump.php';
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class VarDumper
{
    /**
     * @var callable|null
     */
    private static $handler;
    /**
     * @param string|null $label
     *
     * @return mixed
     * @param mixed $var
     */
    public static function dump($var)
    {
        $label = 2 <= \func_num_args() ? \func_get_arg(1) : null;
        if (null === self::$handler) {
            self::register();
        }
        return (self::$handler)($var, $label);
    }
    public static function setHandler(callable $callable = null) : ?callable
    {
        if (1 > \func_num_args()) {
            \ECSPrefix202306\trigger_deprecation('symfony/var-dumper', '6.2', 'Calling "%s()" without any arguments is deprecated, pass null explicitly instead.', __METHOD__);
        }
        $prevHandler = self::$handler;
        // Prevent replacing the handler with expected format as soon as the env var was set:
        if (isset($_SERVER['VAR_DUMPER_FORMAT'])) {
            return $prevHandler;
        }
        self::$handler = $callable;
        return $prevHandler;
    }
    private static function register() : void
    {
        $cloner = new VarCloner();
        $cloner->addCasters(ReflectionCaster::UNSET_CLOSURE_FILE_INFO);
        $format = $_SERVER['VAR_DUMPER_FORMAT'] ?? null;
        switch (\true) {
            case 'html' === $format:
                $dumper = new HtmlDumper();
                break;
            case 'cli' === $format:
                $dumper = new CliDumper();
                break;
            case 'server' === $format:
            case $format && 'tcp' === \parse_url($format, \PHP_URL_SCHEME):
                $host = 'server' === $format ? $_SERVER['VAR_DUMPER_SERVER'] ?? '127.0.0.1:9912' : $format;
                $dumper = \in_array(\PHP_SAPI, ['cli', 'phpdbg'], \true) ? new CliDumper() : new HtmlDumper();
                $dumper = new ServerDumper($host, $dumper, self::getDefaultContextProviders());
                break;
            default:
                $dumper = \in_array(\PHP_SAPI, ['cli', 'phpdbg'], \true) ? new CliDumper() : new HtmlDumper();
        }
        if (!$dumper instanceof ServerDumper) {
            $dumper = new ContextualizedDumper($dumper, [new SourceContextProvider()]);
        }
        self::$handler = function ($var, string $label = null) use($cloner, $dumper) {
            $var = $cloner->cloneVar($var);
            if (null !== $label) {
                $var = $var->withContext(['label' => $label]);
            }
            $dumper->dump($var);
        };
    }
    private static function getDefaultContextProviders() : array
    {
        $contextProviders = [];
        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], \true) && \class_exists(Request::class)) {
            $requestStack = new RequestStack();
            $requestStack->push(Request::createFromGlobals());
            $contextProviders['request'] = new RequestContextProvider($requestStack);
        }
        $fileLinkFormatter = \class_exists(FileLinkFormatter::class) ? new FileLinkFormatter(null, $requestStack ?? null) : null;
        return $contextProviders + ['cli' => new CliContextProvider(), 'source' => new SourceContextProvider(null, null, $fileLinkFormatter)];
    }
}
