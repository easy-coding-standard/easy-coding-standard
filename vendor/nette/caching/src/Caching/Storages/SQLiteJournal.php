<?php

namespace ECSPrefix20210512\Nette\Caching\Storages;

use ECSPrefix20210512\Nette;
use ECSPrefix20210512\Nette\Caching\Cache;
/**
 * SQLite based journal.
 */
class SQLiteJournal implements \ECSPrefix20210512\Nette\Caching\Storages\Journal
{
    use Nette\SmartObject;
    /** @string */
    private $path;
    /** @var \PDO */
    private $pdo;
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $path = (string) $path;
        if (!\extension_loaded('pdo_sqlite')) {
            throw new \ECSPrefix20210512\Nette\NotSupportedException('SQLiteJournal requires PHP extension pdo_sqlite which is not loaded.');
        }
        $this->path = $path;
    }
    /**
     * @return void
     */
    private function open()
    {
        if ($this->path !== ':memory:' && !\is_file($this->path)) {
            \touch($this->path);
            // ensures ordinary file permissions
        }
        $this->pdo = new \PDO('sqlite:' . $this->path);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('
			PRAGMA foreign_keys = OFF;
			PRAGMA journal_mode = WAL;
			CREATE TABLE IF NOT EXISTS tags (
				key BLOB NOT NULL,
				tag BLOB NOT NULL
			);
			CREATE TABLE IF NOT EXISTS priorities (
				key BLOB NOT NULL,
				priority INT NOT NULL
			);
			CREATE INDEX IF NOT EXISTS idx_tags_tag ON tags(tag);
			CREATE UNIQUE INDEX IF NOT EXISTS idx_tags_key_tag ON tags(key, tag);
			CREATE UNIQUE INDEX IF NOT EXISTS idx_priorities_key ON priorities(key);
			CREATE INDEX IF NOT EXISTS idx_priorities_priority ON priorities(priority);
		');
    }
    /**
     * @return void
     * @param string $key
     */
    public function write($key, array $dependencies)
    {
        $key = (string) $key;
        if (!$this->pdo) {
            $this->open();
        }
        $this->pdo->exec('BEGIN');
        if (!empty($dependencies[\ECSPrefix20210512\Nette\Caching\Cache::TAGS])) {
            $this->pdo->prepare('DELETE FROM tags WHERE key = ?')->execute([$key]);
            foreach ($dependencies[\ECSPrefix20210512\Nette\Caching\Cache::TAGS] as $tag) {
                $arr[] = $key;
                $arr[] = $tag;
            }
            $this->pdo->prepare('INSERT INTO tags (key, tag) SELECT ?, ?' . \str_repeat('UNION SELECT ?, ?', \count($arr) / 2 - 1))->execute($arr);
        }
        if (!empty($dependencies[\ECSPrefix20210512\Nette\Caching\Cache::PRIORITY])) {
            $this->pdo->prepare('REPLACE INTO priorities (key, priority) VALUES (?, ?)')->execute([$key, (int) $dependencies[\ECSPrefix20210512\Nette\Caching\Cache::PRIORITY]]);
        }
        $this->pdo->exec('COMMIT');
    }
    /**
     * @return mixed[]|null
     */
    public function clean(array $conditions)
    {
        if (!$this->pdo) {
            $this->open();
        }
        if (!empty($conditions[\ECSPrefix20210512\Nette\Caching\Cache::ALL])) {
            $this->pdo->exec('
				BEGIN;
				DELETE FROM tags;
				DELETE FROM priorities;
				COMMIT;
			');
            return null;
        }
        $unions = $args = [];
        if (!empty($conditions[\ECSPrefix20210512\Nette\Caching\Cache::TAGS])) {
            $tags = (array) $conditions[\ECSPrefix20210512\Nette\Caching\Cache::TAGS];
            $unions[] = 'SELECT DISTINCT key FROM tags WHERE tag IN (?' . \str_repeat(', ?', \count($tags) - 1) . ')';
            $args = $tags;
        }
        if (!empty($conditions[\ECSPrefix20210512\Nette\Caching\Cache::PRIORITY])) {
            $unions[] = 'SELECT DISTINCT key FROM priorities WHERE priority <= ?';
            $args[] = (int) $conditions[\ECSPrefix20210512\Nette\Caching\Cache::PRIORITY];
        }
        if (empty($unions)) {
            return [];
        }
        $unionSql = \implode(' UNION ', $unions);
        $this->pdo->exec('BEGIN IMMEDIATE');
        $stmt = $this->pdo->prepare($unionSql);
        $stmt->execute($args);
        $keys = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
        if (empty($keys)) {
            $this->pdo->exec('COMMIT');
            return [];
        }
        $this->pdo->prepare("DELETE FROM tags WHERE key IN ({$unionSql})")->execute($args);
        $this->pdo->prepare("DELETE FROM priorities WHERE key IN ({$unionSql})")->execute($args);
        $this->pdo->exec('COMMIT');
        return $keys;
    }
}
