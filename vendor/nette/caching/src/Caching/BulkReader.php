<?php

namespace ECSPrefix20210513\Nette\Caching;

/**
 * Cache storage with a bulk read support.
 */
interface BulkReader
{
    /**
     * Reads from cache in bulk.
     * @return array key => value pairs, missing items are omitted
     */
    function bulkRead(array $keys);
}
\class_exists(\ECSPrefix20210513\Nette\Caching\IBulkReader::class);
