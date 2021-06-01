<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\HttpFoundation\Session\Storage;

use ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request;
// Help opcache.preload discover always-needed symbols
\class_exists(\ConfigTransformer20210601\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage::class);
/**
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class NativeSessionStorageFactory implements \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Session\Storage\SessionStorageFactoryInterface
{
    private $options;
    private $handler;
    private $metaBag;
    private $secure;
    /**
     * @see NativeSessionStorage constructor.
     */
    public function __construct(array $options = [], $handler = null, \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Session\Storage\MetadataBag $metaBag = null, bool $secure = \false)
    {
        $this->options = $options;
        $this->handler = $handler;
        $this->metaBag = $metaBag;
        $this->secure = $secure;
    }
    /**
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     */
    public function createStorage($request) : \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface
    {
        $storage = new \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage($this->options, $this->handler, $this->metaBag);
        if ($this->secure && $request && $request->isSecure()) {
            $storage->setOptions(['cookie_secure' => \true]);
        }
        return $storage;
    }
}
