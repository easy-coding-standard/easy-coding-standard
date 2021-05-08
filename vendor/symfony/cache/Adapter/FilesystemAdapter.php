<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Cache\Adapter;

use ECSPrefix20210508\Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface;
use ECSPrefix20210508\Symfony\Component\Cache\PruneableInterface;
use ECSPrefix20210508\Symfony\Component\Cache\Traits\FilesystemTrait;
class FilesystemAdapter extends \ECSPrefix20210508\Symfony\Component\Cache\Adapter\AbstractAdapter implements \ECSPrefix20210508\Symfony\Component\Cache\PruneableInterface
{
    use FilesystemTrait;
    /**
     * @param string $namespace
     */
    public function __construct($namespace = '', int $defaultLifetime = 0, string $directory = null, \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface $marshaller = null)
    {
        if (\is_object($namespace)) {
            $namespace = (string) $namespace;
        }
        $this->marshaller = isset($marshaller) ? $marshaller : new \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\DefaultMarshaller();
        parent::__construct('', $defaultLifetime);
        $this->init($namespace, $directory);
    }
}
