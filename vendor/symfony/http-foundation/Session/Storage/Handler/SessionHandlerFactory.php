<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler;

use ECSPrefix20211002\Doctrine\DBAL\DriverManager;
use ECSPrefix20211002\Symfony\Component\Cache\Adapter\AbstractAdapter;
use ECSPrefix20211002\Symfony\Component\Cache\Traits\RedisClusterProxy;
use ECSPrefix20211002\Symfony\Component\Cache\Traits\RedisProxy;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class SessionHandlerFactory
{
    /**
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\ClientInterface|RedisProxy|RedisClusterProxy|\Memcached|\PDO|string $connection Connection or DSN
     */
    public static function createHandler($connection) : \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\AbstractSessionHandler
    {
        if (!\is_string($connection) && !\is_object($connection)) {
            throw new \TypeError(\sprintf('Argument 1 passed to "%s()" must be a string or a connection object, "%s" given.', __METHOD__, \get_debug_type($connection)));
        }
        switch (\true) {
            case $connection instanceof \Redis:
            case $connection instanceof \RedisArray:
            case $connection instanceof \RedisCluster:
            case $connection instanceof \ECSPrefix20211002\Predis\ClientInterface:
            case $connection instanceof \ECSPrefix20211002\Symfony\Component\Cache\Traits\RedisProxy:
            case $connection instanceof \ECSPrefix20211002\Symfony\Component\Cache\Traits\RedisClusterProxy:
                return new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler($connection);
            case $connection instanceof \Memcached:
                return new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler($connection);
            case $connection instanceof \PDO:
                return new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler($connection);
            case !\is_string($connection):
                throw new \InvalidArgumentException(\sprintf('Unsupported Connection: "%s".', \get_debug_type($connection)));
            case \strncmp($connection, 'file://', \strlen('file://')) === 0:
                $savePath = \substr($connection, 7);
                return new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler(new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler('' === $savePath ? null : $savePath));
            case \strncmp($connection, 'redis:', \strlen('redis:')) === 0:
            case \strncmp($connection, 'rediss:', \strlen('rediss:')) === 0:
            case \strncmp($connection, 'memcached:', \strlen('memcached:')) === 0:
                if (!\class_exists(\ECSPrefix20211002\Symfony\Component\Cache\Adapter\AbstractAdapter::class)) {
                    throw new \InvalidArgumentException(\sprintf('Unsupported DSN "%s". Try running "composer require symfony/cache".', $connection));
                }
                $handlerClass = \strncmp($connection, 'memcached:', \strlen('memcached:')) === 0 ? \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler::class : \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler::class;
                $connection = \ECSPrefix20211002\Symfony\Component\Cache\Adapter\AbstractAdapter::createConnection($connection, ['lazy' => \true]);
                return new $handlerClass($connection);
            case \strncmp($connection, 'pdo_oci://', \strlen('pdo_oci://')) === 0:
                if (!\class_exists(\ECSPrefix20211002\Doctrine\DBAL\DriverManager::class)) {
                    throw new \InvalidArgumentException(\sprintf('Unsupported DSN "%s". Try running "composer require doctrine/dbal".', $connection));
                }
                $connection = \ECSPrefix20211002\Doctrine\DBAL\DriverManager::getConnection(['url' => $connection])->getWrappedConnection();
            // no break;
            case \strncmp($connection, 'mssql://', \strlen('mssql://')) === 0:
            case \strncmp($connection, 'mysql://', \strlen('mysql://')) === 0:
            case \strncmp($connection, 'mysql2://', \strlen('mysql2://')) === 0:
            case \strncmp($connection, 'pgsql://', \strlen('pgsql://')) === 0:
            case \strncmp($connection, 'postgres://', \strlen('postgres://')) === 0:
            case \strncmp($connection, 'postgresql://', \strlen('postgresql://')) === 0:
            case \strncmp($connection, 'sqlsrv://', \strlen('sqlsrv://')) === 0:
            case \strncmp($connection, 'sqlite://', \strlen('sqlite://')) === 0:
            case \strncmp($connection, 'sqlite3://', \strlen('sqlite3://')) === 0:
                return new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler($connection);
        }
        throw new \InvalidArgumentException(\sprintf('Unsupported Connection: "%s".', $connection));
    }
}
