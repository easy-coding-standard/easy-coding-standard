<?php

namespace Nette\Caching;

if (false) {
	/** @deprecated use Nette\Caching\BulkReader */
	interface IBulkReader extends BulkReader
	{
	}
} elseif (!interface_exists(IBulkReader::class)) {
	class_alias(BulkReader::class, IBulkReader::class);
}

if (false) {
	/** @deprecated use Nette\Caching\Storage */
	interface IStorage extends Storage
	{
	}
} elseif (!interface_exists(IStorage::class)) {
	class_alias(Storage::class, IStorage::class);
}

namespace Nette\Caching\Storages;

if (false) {
	/** @deprecated use Nette\Caching\Storages\Journal */
	interface IJournal extends Journal
	{
	}
} elseif (!interface_exists(IJournal::class)) {
	class_alias(Journal::class, IJournal::class);
}
