<?php

namespace ECSPrefix20210515\Nette\Caching\Storages;

/**
 * Cache journal provider.
 */
interface Journal
{
    /**
     * Writes entry information into the journal.
     * @return void
     * @param string $key
     */
    function write($key, array $dependencies);
    /**
     * Cleans entries from journal.
     * @return array|null of removed items or null when performing a full cleanup
     */
    function clean(array $conditions);
}
\class_exists(\ECSPrefix20210515\Nette\Caching\Storages\IJournal::class);
