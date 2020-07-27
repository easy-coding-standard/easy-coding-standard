<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Set;

use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SetConfigResolver\ValueObject\Set;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardSetProvider implements SetProviderInterface
{
    /**
     * @var Set[]
     */
    private $sets = [];

    public function __construct()
    {
        $setNameToFilePath = [
            SetList::SYMPLIFY => __DIR__ . '/../../config/set/symplify.php',
            SetList::CLEAN_CODE => __DIR__ . '/../../config/set/clean-code.php',
            SetList::PHP_70 => __DIR__ . '/../../config/set/php70.php',
            SetList::PHP_71 => __DIR__ . '/../../config/set/php71.php',
            SetList::PSR_12 => __DIR__ . '/../../config/set/psr12.php',
            SetList::DEAD_CODE => __DIR__ . '/../../config/set/dead-code.php',
            SetList::SYMFONY => __DIR__ . '/../../config/set/symfony.php',
            SetList::SYMFONY_RISKY => __DIR__ . '/../../config/set/symfony-risky.php',
            // common
            SetList::COMMON => __DIR__ . '/../../config/set/common.php',
            SetList::ARRAY => __DIR__ . '/../../config/set/common/array.php',
            SetList::SPACES => __DIR__ . '/../../config/set/common/spaces.php',
            SetList::NAMESPACES => __DIR__ . '/../../config/set/common/namespaces.php',
            SetList::CONTROL_STRUCTURES => __DIR__ . '/../../config/set/common/control-structures.php',
            SetList::COMMENTS => __DIR__ . '/../../config/set/common/comments.php',
            SetList::DOCBLOCK => __DIR__ . '/../../config/set/common/docblock.php',
            SetList::PHPUNIT => __DIR__ . '/../../config/set/common/phpunit.php',
        ];

        foreach ($setNameToFilePath as $setName => $setFilePath) {
            $this->sets[] = new Set($setName, new SmartFileInfo($setFilePath));
        }
    }

    /**
     * @return Set[]
     */
    public function provide(): array
    {
        return $this->sets;
    }

    /**
     * @return string[]
     */
    public function provideSetNames(): array
    {
        $setNames = [];
        foreach ($this->sets as $set) {
            $setNames[] = $set->getName();
        }

        sort($setNames);

        return $setNames;
    }

    public function provideByName(string $setName): ?Set
    {
        foreach ($this->sets as $set) {
            if ($set->getName() !== $setName) {
                continue;
            }

            return $set;
        }

        return null;
    }
}
