<?php

namespace ECSPrefix20210517\Nette\Caching;

if (\false) {
    /** @deprecated use Nette\Caching\BulkReader */
    interface IBulkReader extends \ECSPrefix20210517\Nette\Caching\BulkReader
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210517\Nette\Caching\IBulkReader::class)) {
    \class_alias(\ECSPrefix20210517\Nette\Caching\BulkReader::class, \ECSPrefix20210517\Nette\Caching\IBulkReader::class);
}
if (\false) {
    /** @deprecated use Nette\Caching\Storage */
    interface IStorage extends \ECSPrefix20210517\Nette\Caching\Storage
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210517\Nette\Caching\IStorage::class)) {
    \class_alias(\ECSPrefix20210517\Nette\Caching\Storage::class, \ECSPrefix20210517\Nette\Caching\IStorage::class);
}
namespace ECSPrefix20210517\Nette\Caching\Storages;

if (\false) {
    /** @deprecated use Nette\Caching\Storages\Journal */
    interface IJournal extends \ECSPrefix20210517\Nette\Caching\Storages\Journal
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210517\Nette\Caching\Storages\IJournal::class)) {
    \class_alias(\ECSPrefix20210517\Nette\Caching\Storages\Journal::class, \ECSPrefix20210517\Nette\Caching\Storages\IJournal::class);
}
