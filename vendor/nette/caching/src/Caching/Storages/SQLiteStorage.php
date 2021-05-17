<?php

namespace ECSPrefix20210517\Nette\Caching\Storages;

use ECSPrefix20210517\Nette;
use ECSPrefix20210517\Nette\Caching\Cache;
/**
 * SQLite storage.
 */
class SQLiteStorage implements \ECSPrefix20210517\Nette\Caching\Storage, \ECSPrefix20210517\Nette\Caching\BulkReader
{
    use Nette\SmartObject;
    /** @var \PDO */
    private $pdo;
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $path = (string) $path;
        if ($path !== ':memory:' && !\is_file($path)) {
            \touch($path);
            // ensures ordinary file permissions
        }
        $this->pdo = new \PDO('sqlite:' . $path);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('
			PRAGMA foreign_keys = ON;
			CREATE TABLE IF NOT EXISTS cache (
				key BLOB NOT NULL PRIMARY KEY,
				data BLOB NOT NULL,
				expire INTEGER,
				slide INTEGER
			);
			CREATE TABLE IF NOT EXISTS tags (
				key BLOB NOT NULL REFERENCES cache ON DELETE CASCADE,
				tag BLOB NOT NULL
			);
			CREATE INDEX IF NOT EXISTS cache_expire ON cache(expire);
			CREATE INDEX IF NOT EXISTS tags_key ON tags(key);
			CREATE INDEX IF NOT EXISTS tags_tag ON tags(tag);
			PRAGMA synchronous = OFF;
		');
    }
    /**
     * @param string $key
     */
    public function read($key)
    {
        $key = (string) $key;
        $stmt = $this->pdo->prepare('SELECT data, slide FROM cache WHERE key=? AND (expire IS NULL OR expire >= ?)');
        $stmt->execute([$key, \time()]);
        if (!($row = $stmt->fetch(\PDO::FETCH_ASSOC))) {
            return;
        }
        if ($row['slide'] !== null) {
            $this->pdo->prepare('UPDATE cache SET expire = ? + slide WHERE key=?')->execute([\time(), $key]);
        }
        return \unserialize($row['data']);
    }
    /**
     * @return mixed[]
     */
    public function bulkRead(array $keys)
    {
        $stmt = $this->pdo->prepare('SELECT key, data, slide FROM cache WHERE key IN (?' . \str_repeat(',?', \count($keys) - 1) . ') AND (expire IS NULL OR expire >= ?)');
        $stmt->execute(\array_merge($keys, [\time()]));
        $result = [];
        $updateSlide = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if ($row['slide'] !== null) {
                $updateSlide[] = $row['key'];
            }
            $result[$row['key']] = \unserialize($row['data']);
        }
        if (!empty($updateSlide)) {
            $stmt = $this->pdo->prepare('UPDATE cache SET expire = ? + slide WHERE key IN(?' . \str_repeat(',?', \count($updateSlide) - 1) . ')');
            $stmt->execute(\array_merge([\time()], $updateSlide));
        }
        return $result;
    }
    /**
     * @return void
     * @param string $key
     */
    public function lock($key)
    {
    }
    /**
     * @return void
     * @param string $key
     */
    public function write($key, $data, array $dependencies)
    {
        $key = (string) $key;
        $expire = isset($dependencies[\ECSPrefix20210517\Nette\Caching\Cache::EXPIRATION]) ? $dependencies[\ECSPrefix20210517\Nette\Caching\Cache::EXPIRATION] + \time() : null;
        $slide = isset($dependencies[\ECSPrefix20210517\Nette\Caching\Cache::SLIDING]) ? $dependencies[\ECSPrefix20210517\Nette\Caching\Cache::EXPIRATION] : null;
        $this->pdo->exec('BEGIN TRANSACTION');
        $this->pdo->prepare('REPLACE INTO cache (key, data, expire, slide) VALUES (?, ?, ?, ?)')->execute([$key, \serialize($data), $expire, $slide]);
        if (!empty($dependencies[\ECSPrefix20210517\Nette\Caching\Cache::TAGS])) {
            foreach ($dependencies[\ECSPrefix20210517\Nette\Caching\Cache::TAGS] as $tag) {
                $arr[] = $key;
                $arr[] = $tag;
            }
            $this->pdo->prepare('INSERT INTO tags (key, tag) SELECT ?, ?' . \str_repeat('UNION SELECT ?, ?', \count($arr) / 2 - 1))->execute($arr);
        }
        $this->pdo->exec('COMMIT');
    }
    /**
     * @return void
     * @param string $key
     */
    public function remove($key)
    {
        $key = (string) $key;
        $this->pdo->prepare('DELETE FROM cache WHERE key=?')->execute([$key]);
    }
    /**
     * @return void
     */
    public function clean(array $conditions)
    {
        if (!empty($conditions[\ECSPrefix20210517\Nette\Caching\Cache::ALL])) {
            $this->pdo->prepare('DELETE FROM cache')->execute();
        } else {
            $sql = 'DELETE FROM cache WHERE expire < ?';
            $args = [\time()];
            if (!empty($conditions[\ECSPrefix20210517\Nette\Caching\Cache::TAGS])) {
                $tags = $conditions[\ECSPrefix20210517\Nette\Caching\Cache::TAGS];
                $sql .= ' OR key IN (SELECT key FROM tags WHERE tag IN (?' . \str_repeat(',?', \count($tags) - 1) . '))';
                $args = \array_merge($args, $tags);
            }
            $this->pdo->prepare($sql)->execute($args);
        }
    }
}
