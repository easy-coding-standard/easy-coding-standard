<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpKernel\Debug;

use ECSPrefix202306\Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202306\Symfony\Component\HttpFoundation\RequestStack;
use ECSPrefix202306\Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/**
 * Formats debug file links.
 *
 * @author Jérémy Romey <jeremy@free-agent.fr>
 *
 * @final
 */
class FileLinkFormatter
{
    /**
     * @var mixed[]|false
     */
    private $fileLinkFormat;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack|null
     */
    private $requestStack;
    /**
     * @var string|null
     */
    private $baseDir;
    /**
     * @var \Closure|string|null
     */
    private $urlFormat;
    /**
     * @param string|\Closure $urlFormat the URL format, or a closure that returns it on-demand
     * @param string|mixed[] $fileLinkFormat
     */
    public function __construct($fileLinkFormat = null, RequestStack $requestStack = null, string $baseDir = null, $urlFormat = null)
    {
        $fileLinkFormat = $fileLinkFormat ?? $_ENV['SYMFONY_IDE'] ?? $_SERVER['SYMFONY_IDE'] ?? '';
        if (!\is_array($fileLinkFormat) && ($fileLinkFormat = ((ErrorRendererInterface::IDE_LINK_FORMATS[$fileLinkFormat] ?? $fileLinkFormat ?: \ini_get('xdebug.file_link_format')) ?: \get_cfg_var('xdebug.file_link_format')) ?: \false)) {
            $i = \strpos($f = $fileLinkFormat, '&', \max(\strrpos($f, '%f'), \strrpos($f, '%l'))) ?: \strlen($f);
            $fileLinkFormat = [\substr($f, 0, $i)] + \preg_split('/&([^>]++)>/', \substr($f, $i), -1, \PREG_SPLIT_DELIM_CAPTURE);
        }
        $this->fileLinkFormat = $fileLinkFormat;
        $this->requestStack = $requestStack;
        $this->baseDir = $baseDir;
        $this->urlFormat = $urlFormat;
    }
    public function format(string $file, int $line)
    {
        if ($fmt = $this->getFileLinkFormat()) {
            for ($i = 1; isset($fmt[$i]); ++$i) {
                if (\strncmp($file, $k = $fmt[$i++], \strlen($k = $fmt[$i++])) === 0) {
                    $file = \substr_replace($file, $fmt[$i], 0, \strlen($k));
                    break;
                }
            }
            return \strtr($fmt[0], ['%f' => $file, '%l' => $line]);
        }
        return \false;
    }
    /**
     * @internal
     */
    public function __sleep() : array
    {
        $this->fileLinkFormat = $this->getFileLinkFormat();
        return ['fileLinkFormat'];
    }
    /**
     * @internal
     */
    public static function generateUrlFormat(UrlGeneratorInterface $router, string $routeName, string $queryString) : ?string
    {
        try {
            return $router->generate($routeName) . $queryString;
        } catch (\Throwable $exception) {
            return null;
        }
    }
    /**
     * @return mixed[]|false
     */
    private function getFileLinkFormat()
    {
        if ($this->fileLinkFormat) {
            return $this->fileLinkFormat;
        }
        if ($this->requestStack && $this->baseDir && $this->urlFormat) {
            $request = $this->requestStack->getMainRequest();
            if ($request instanceof Request && (!$this->urlFormat instanceof \Closure || ($this->urlFormat = ($this->urlFormat)()))) {
                return [$request->getSchemeAndHttpHost() . $this->urlFormat, $this->baseDir . \DIRECTORY_SEPARATOR, ''];
            }
        }
        return \false;
    }
}
