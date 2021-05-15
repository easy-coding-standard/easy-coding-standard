<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210515\Symfony\Component\HttpKernel\Debug;

use ECSPrefix20210515\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210515\Symfony\Component\HttpFoundation\RequestStack;
use ECSPrefix20210515\Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/**
 * Formats debug file links.
 *
 * @author Jérémy Romey <jeremy@free-agent.fr>
 *
 * @final
 */
class FileLinkFormatter
{
    private $fileLinkFormat;
    private $requestStack;
    private $baseDir;
    private $urlFormat;
    /**
     * @param string|\Closure $urlFormat the URL format, or a closure that returns it on-demand
     * @param string $baseDir
     */
    public function __construct($fileLinkFormat = null, \ECSPrefix20210515\Symfony\Component\HttpFoundation\RequestStack $requestStack = null, $baseDir = null, $urlFormat = null)
    {
        $fileLinkFormat = ($fileLinkFormat ?: \ini_get('xdebug.file_link_format')) ?: \get_cfg_var('xdebug.file_link_format');
        if ($fileLinkFormat && !\is_array($fileLinkFormat)) {
            $i = \strpos($f = $fileLinkFormat, '&', \max(\strrpos($f, '%f'), \strrpos($f, '%l'))) ?: \strlen($f);
            $fileLinkFormat = [\substr($f, 0, $i)] + \preg_split('/&([^>]++)>/', \substr($f, $i), -1, \PREG_SPLIT_DELIM_CAPTURE);
        }
        $this->fileLinkFormat = $fileLinkFormat;
        $this->requestStack = $requestStack;
        $this->baseDir = $baseDir;
        $this->urlFormat = $urlFormat;
    }
    /**
     * @param string $file
     * @param int $line
     */
    public function format($file, $line)
    {
        $file = (string) $file;
        $line = (int) $line;
        if ($fmt = $this->getFileLinkFormat()) {
            for ($i = 1; isset($fmt[$i]); ++$i) {
                if (0 === \strpos($file, $k = $fmt[$i++])) {
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
     * @return mixed[]
     */
    public function __sleep()
    {
        $this->fileLinkFormat = $this->getFileLinkFormat();
        return ['fileLinkFormat'];
    }
    /**
     * @internal
     * @return string|null
     * @param string $routeName
     * @param string $queryString
     */
    public static function generateUrlFormat(\ECSPrefix20210515\Symfony\Component\Routing\Generator\UrlGeneratorInterface $router, $routeName, $queryString)
    {
        $routeName = (string) $routeName;
        $queryString = (string) $queryString;
        try {
            return $router->generate($routeName) . $queryString;
        } catch (\Throwable $e) {
            return null;
        }
    }
    private function getFileLinkFormat()
    {
        if ($this->fileLinkFormat) {
            return $this->fileLinkFormat;
        }
        if ($this->requestStack && $this->baseDir && $this->urlFormat) {
            $request = $this->requestStack->getMasterRequest();
            if ($request instanceof \ECSPrefix20210515\Symfony\Component\HttpFoundation\Request && (!$this->urlFormat instanceof \Closure || ($this->urlFormat = ($this->urlFormat)()))) {
                return [$request->getSchemeAndHttpHost() . $this->urlFormat, $this->baseDir . \DIRECTORY_SEPARATOR, ''];
            }
        }
        return null;
    }
}
