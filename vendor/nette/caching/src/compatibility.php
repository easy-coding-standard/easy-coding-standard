<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20210523\Nette\Caching;

if (\false) {
    /** @deprecated use Nette\Caching\BulkReader */
    interface IBulkReader extends \ECSPrefix20210523\Nette\Caching\BulkReader
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210523\Nette\Caching\IBulkReader::class)) {
    \class_alias(\ECSPrefix20210523\Nette\Caching\BulkReader::class, \ECSPrefix20210523\Nette\Caching\IBulkReader::class);
}
if (\false) {
    /** @deprecated use Nette\Caching\Storage */
    interface IStorage extends \ECSPrefix20210523\Nette\Caching\Storage
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210523\Nette\Caching\IStorage::class)) {
    \class_alias(\ECSPrefix20210523\Nette\Caching\Storage::class, \ECSPrefix20210523\Nette\Caching\IStorage::class);
}
namespace ECSPrefix20210523\Nette\Caching\Storages;

if (\false) {
    /** @deprecated use Nette\Caching\Storages\Journal */
    interface IJournal extends \ECSPrefix20210523\Nette\Caching\Storages\Journal
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210523\Nette\Caching\Storages\IJournal::class)) {
    \class_alias(\ECSPrefix20210523\Nette\Caching\Storages\Journal::class, \ECSPrefix20210523\Nette\Caching\Storages\IJournal::class);
}
