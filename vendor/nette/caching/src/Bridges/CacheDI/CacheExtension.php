<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20210605\Nette\Bridges\CacheDI;

use ECSPrefix20210605\Nette;
/**
 * Cache extension for Nette DI.
 */
final class CacheExtension extends \ECSPrefix20210605\Nette\DI\CompilerExtension
{
    /** @var string */
    private $tempDir;
    public function __construct(string $tempDir)
    {
        $this->tempDir = $tempDir;
    }
    public function loadConfiguration()
    {
        $dir = $this->tempDir . '/cache';
        \ECSPrefix20210605\Nette\Utils\FileSystem::createDir($dir);
        if (!\is_writable($dir)) {
            throw new \ECSPrefix20210605\Nette\InvalidStateException("Make directory '{$dir}' writable.");
        }
        $builder = $this->getContainerBuilder();
        if (\extension_loaded('pdo_sqlite')) {
            $builder->addDefinition($this->prefix('journal'))->setType(\ECSPrefix20210605\Nette\Caching\Storages\Journal::class)->setFactory(\ECSPrefix20210605\Nette\Caching\Storages\SQLiteJournal::class, [$dir . '/journal.s3db']);
        }
        $builder->addDefinition($this->prefix('storage'))->setType(\ECSPrefix20210605\Nette\Caching\Storage::class)->setFactory(\ECSPrefix20210605\Nette\Caching\Storages\FileStorage::class, [$dir]);
        if ($this->name === 'cache') {
            if (\extension_loaded('pdo_sqlite')) {
                $builder->addAlias('nette.cacheJournal', $this->prefix('journal'));
            }
            $builder->addAlias('cacheStorage', $this->prefix('storage'));
        }
    }
}
