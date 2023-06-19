<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpKernel\Config;

use ECSPrefix202306\Symfony\Component\Config\FileLocator as BaseFileLocator;
use ECSPrefix202306\Symfony\Component\HttpKernel\KernelInterface;
/**
 * FileLocator uses the KernelInterface to locate resources in bundles.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileLocator extends BaseFileLocator
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        parent::__construct();
    }
    /**
     * {@inheritdoc}
     * @return string|mixed[]
     */
    public function locate(string $file, string $currentPath = null, bool $first = \true)
    {
        if (isset($file[0]) && '@' === $file[0]) {
            $resource = $this->kernel->locateResource($file);
            return $first ? $resource : [$resource];
        }
        return parent::locate($file, $currentPath, $first);
    }
}
